<template>
  <div v-if="show" class="modal-backdrop" @click="$emit('close')">
    <div class="modal-content" @click.stop>
      <div class="modal-header">
        <h3>Upload de Imagem</h3>
        <button class="btn btn-small btn-secondary" @click="$emit('close')">✕</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Escolha uma imagem (JPG/PNG, máx 5MB):</label>
          <input type="file" @change="handleFileChange" accept="image/jpeg,image/jpg,image/png" ref="fileInput">
        </div>
        <div v-if="preview" style="margin-top:12px;">
          <label>Preview:</label>
          <div style="margin-top:8px; text-align:center;">
            <img :src="preview" alt="Preview" style="max-width:200px; max-height:200px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
          </div>
        </div>
        <div v-if="uploading" style="margin-top:12px; color:#666;">
          <span>Enviando...</span>
        </div>
        <div v-if="error" style="margin-top:12px; color:#d9534f;">
          <span>{{ error }}</span>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn" @click="upload" :disabled="!selectedFile || uploading">Enviar</button>
        <button class="btn btn-secondary" @click="$emit('close')" :disabled="uploading">Cancelar</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UploadModal',
  props: {
    show: Boolean,
  },
  data() {
    return {
      selectedFile: null,
      preview: null,
      uploading: false,
      error: null,
    };
  },
  methods: {
    handleFileChange(e) {
      const file = e.target.files[0];
      if (!file) return;
      if (!file.type.match(/^image\/(jpeg|jpg|png)$/)) {
        this.error = 'Apenas JPG/PNG são permitidos.';
        this.selectedFile = null;
        this.preview = null;
        return;
      }
      if (file.size > 5 * 1024 * 1024) {
        this.error = 'Tamanho máximo: 5MB.';
        this.selectedFile = null;
        this.preview = null;
        return;
      }
      this.error = null;
      this.selectedFile = file;
      const reader = new FileReader();
      reader.onload = (e) => {
        this.preview = e.target.result;
      };
      reader.readAsDataURL(file);
    },
    async upload() {
      if (!this.selectedFile) return;
      this.uploading = true;
      this.error = null;
      try {
        const fileData = await new Promise((resolve, reject) => {
          const reader = new FileReader();
          reader.onload = () => resolve(String(reader.result || ''));
          reader.onerror = () => reject(new Error('Falha ao ler arquivo'));
          reader.readAsDataURL(this.selectedFile);
        });

        const payload = {
          query:
            'mutation UploadImage($target: String!, $filename: String!, $fileData: String!) { uploadImage(target: $target, filename: $filename, fileData: $fileData) }',
          variables: {
            target: 'image',
            filename: this.selectedFile.name || 'upload',
            fileData,
          },
        };

        const response = await fetch('/graphql', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload),
        });

        if (!response.ok) throw new Error('Falha no upload');
        const json = await response.json();
        const publicId = json && json.data ? json.data.uploadImage : null;
        if (!publicId) throw new Error('Falha no upload');

        this.$emit('uploaded', publicId);
        this.$emit('close');
      } catch (err) {
        this.error = err.message || 'Erro ao enviar imagem.';
      } finally {
        this.uploading = false;
      }
    },
  },
};
</script>
