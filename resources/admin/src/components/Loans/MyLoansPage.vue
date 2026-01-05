<template>
  <div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
      <h2>📚 Meus Empréstimos</h2>
      <button class="btn btn-small btn-secondary" @click="$emit('goBack')" style="width:auto;">← Voltar para Livros</button>
    </div>
    <div style="margin-bottom: 10px; max-width: 260px;">
      <select :value="userLoansFilter" @change="$emit('update:user-loans-filter', $event.target.value)" style="width:100%; padding:8px; border:1px solid #dee2e6; border-radius:6px;">
        <option value="all">Todos</option>
        <option value="active">Ativos</option>
        <option value="returned">Devolvidos</option>
        <option value="overdue">Atrasados</option>
      </select>
    </div>

    <div v-if="filteredUserLoans.length > 0">
      <div v-for="loan in filteredUserLoans" :key="loan.id" style="padding: 12px 16px; background: white; border-radius: 10px; margin-bottom: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.06); border: 1px solid #e5e7eb;">
        <div style="display:flex; gap:16px; align-items: center; cursor:pointer;" @click="loan.book && $emit('viewBook', loan.book)">
          <div style="width:72px; height:96px; border-radius:6px; overflow:hidden; background:#f3f4f6; flex-shrink:0;">
            <img v-if="loan.book && loan.book.photo" :src="thumb(loan.book.photo, 300, 420)" :alt="loan.book.title" style="width:100%; height:100%; object-fit:cover;">
            <div v-else style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#9ca3af; font-size:2rem;">📚</div>
          </div>
          <div style="flex:1; display:flex; justify-content: space-between; align-items: center; gap:12px;">
            <div>
              <div style="font-weight:600; color:#162c74; font-size:0.98rem; margin-bottom:4px;">{{ loan.book && loan.book.title ? loan.book.title : 'Livro desconhecido' }}</div>
              <div style="color:#666; font-size:0.85rem;">Emprestado em: {{ formatDate(loan.loan_date) }} <span v-if="loan.return_date">• Devolução: {{ formatDate(loan.return_date) }}</span></div>
            </div>
            <div style="display:flex; flex-direction:column; align-items:flex-end; gap:6px; min-width: 160px;">
              <span v-if="loan.returned_at" style="display:inline-block; padding:3px 10px; border-radius:999px; background:#d4edda; color:#155724; font-size:0.8rem; font-weight:600;">✅ Devolvido</span>
              <span v-else-if="isLoanOverdue(loan)" style="display:inline-block; padding:3px 10px; border-radius:999px; background:#f8d7da; color:#721c24; font-size:0.8rem; font-weight:600;">⚠️ Atrasado</span>
              <span v-else style="display:inline-block; padding:3px 10px; border-radius:999px; background:#fff3cd; color:#856404; font-size:0.8rem; font-weight:600;">📚 Ativo</span>
              <div v-if="typeof loan.days_remaining === 'number' && !isNaN(loan.days_remaining)" style="font-size:0.8rem; color:#666;">
                {{ loan.days_remaining >= 0 ? (loan.days_remaining + ' dia(s) restante(s)') : (Math.abs(loan.days_remaining) + ' dia(s) em atraso') }}
              </div>
              <div style="display:flex; gap:8px;">
                <button v-if="!loan.returned_at" class="btn btn-small btn-danger" @click.stop="$emit('confirm-return-my-loan', loan.id)">Devolver</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <p v-else style="color: #666;">Você não possui empréstimos neste filtro.</p>
  </div>
</template>

<script>
export default {
  name: 'MyLoansPage',
  props: {
    userLoansFilter: { type: String, required: true },
    filteredUserLoans: { type: Array, required: true },
    thumb: { type: Function, required: true },
    formatDate: { type: Function, required: true },
    isLoanOverdue: { type: Function, required: true },
  },
};
</script>
