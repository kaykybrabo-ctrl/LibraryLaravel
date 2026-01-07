<template>
  <div class="container">
    <div style="display:flex; justify-content: space-between; align-items:center; margin-bottom: 12px;">
      <h2>{{ $t('profile.title') }}</h2>
      <button class="btn btn-small" @click="$emit('toggle-editing')">
        {{ editingProfile ? $t('profile.closeEditing') : $t('profile.editProfile') }}
      </button>
    </div>

    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
      <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 20px;">
        <div style="width: 100px; height: 100px; border-radius: 50%; background: #f0f0f0; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; overflow: hidden;">
          <img
            v-if="profileFormPhoto || (currentUser && currentUser.photo)"
            :src="thumb(profileFormPhoto || currentUser.photo, 100, 100)"
            alt="avatar"
            style="width:100%; height:100%; object-fit: cover;"
          >
          <span v-else aria-hidden="true">U</span>
        </div>
        <div>
          <h3 style="margin: 0 0 5px 0; color: #162c74;">{{ currentUser.name }}</h3>
          <p style="margin: 0; color: #666;">{{ currentUser.email }}</p>
          <span style="display: inline-block; margin-top: 5px; padding: 3px 8px; background: #e9ecef; border-radius: 12px; font-size: 0.8rem; color: #495057;">
            {{ currentUser.is_admin ? $t('users.roleAdmin') : $t('users.roleUser') }}
          </span>
        </div>
      </div>

      <div v-if="editingProfile" style="margin-bottom: 24px;">
        <h3 style="margin: 0 0 12px 0; color: #162c74;">{{ $t('profile.editSectionTitle') }}</h3>
        <div class="form-group">
          <label>{{ $t('auth.name') }}:</label>
          <input type="text" :value="profileFormName" @input="$emit('update:profile-form-name', $event.target.value)" required>
        </div>
        <div class="form-group">
          <label>{{ $t('profile.photoLabel') }}</label>
          <input type="text" :value="profileFormPhoto" @input="$emit('update:profile-form-photo', $event.target.value)">
          <div class="mt-3">
            <input
              ref="profileFileInput"
              type="file"
              accept="image/*"
              style="display:none;"
              @change="$emit('upload-profile-file', $event)"
            >
            <button type="button" class="btn btn-small" @click="$refs.profileFileInput && $refs.profileFileInput.click()">{{ $t('common.upload') }}</button>
          </div>
        </div>
        <div style="display:flex; gap:8px; margin-top:12px;">
          <button type="button" class="btn btn-small" @click="$emit('save-profile')" style="width:auto;">{{ $t('common.save') }}</button>
          <button type="button" class="btn btn-small btn-secondary" @click="$emit('toggle-editing')" style="width:auto;">{{ $t('common.cancel') }}</button>
        </div>
      </div>

      <div style="margin-top: 24px;">
        <h3 style="margin: 0 0 10px 0; color: #162c74;">{{ $t('profile.favoriteBookTitle') }}</h3>
        <div v-if="!currentUser.is_admin">
          <div v-if="userFavoriteBook" style="background: #f8f9fa; padding: 15px; border-radius: 8px; display:flex; gap:16px; align-items:center;">
            <div style="width:72px; height:96px; border-radius:6px; overflow:hidden; background:#f3f4f6; flex-shrink:0;">
              <img
                v-if="userFavoriteBook.photo"
                :src="thumb(userFavoriteBook.photo, 300, 420, 'book')"
                :alt="userFavoriteBook.title"
                @error="$event.target.src = thumb('', 300, 420, 'book')"
                style="width:100%; height:100%; object-fit:cover;"
              >
              <div v-else style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#9ca3af; font-size:2rem;">
                B
              </div>
            </div>
            <div style="flex:1;">
              <h4 @click="$emit('view-book', userFavoriteBook)" style="margin: 0 0 5px 0; color: #162c74; cursor:pointer; text-decoration: underline;">{{ userFavoriteBook.title }}</h4>
              <p style="margin: 0; color: #666; font-size: 0.9rem;">
                <span
                  v-if="userFavoriteBook.author"
                  @click="$emit('select-author', userFavoriteBook.author)"
                  style="cursor:pointer;color:#162c74;text-decoration:underline;"
                >
                  {{ userFavoriteBook.author.name }}
                </span>
                <span v-else>{{ $t('books.unknownAuthor') }}</span>
              </p>
              <div style="margin-top: 10px; display:flex; gap:8px;">
                <button class="btn btn-small btn-secondary" @click="$emit('removeFavorite')" style="width:auto;">{{ $t('books.removeFromFavorites') }}</button>
              </div>
            </div>
          </div>
          <p v-else style="color: #666;">{{ $t('profile.noFavoriteBook') }}</p>
        </div>
        <div v-else style="color: #666; font-size: 0.9rem;">
          {{ $t('profile.adminNoFavorite') }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProfilePage',
  props: {
    currentUser: { type: Object, required: true },
    editingProfile: { type: Boolean, required: true },
    profileFormName: { type: String, required: true },
    profileFormPhoto: { type: String, required: true },
    userFavoriteBook: { type: Object, default: null },
    thumb: { type: Function, required: true },
  },
};
</script>
