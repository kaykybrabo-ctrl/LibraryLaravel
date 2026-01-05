<template>
  <div class="home-root">
    <div class="home-header">
      <div class="header-container">
        <div class="brand" @click="scrollToTop" role="button" tabindex="0">
          <span class="logo"></span>
          <h1>PedBook</h1>
        </div>
        <nav class="home-nav">
          <a href="/app#books" class="nav-link">Livros</a>
          <a href="/app#authors" class="nav-link">Autores</a>
          <a href="#highlights" class="nav-link" @click.prevent="scrollToHighlights">Destaques</a>
          <a href="/login" class="nav-link login-btn">Entrar</a>
        </nav>
      </div>
    </div>

    <div class="carousel-container">
      <div
        v-for="(slide, index) in slides"
        :key="index"
        :class="['carousel-slide', { active: currentSlide === index }]"
        :style="{ backgroundImage: `url('${slide.image}')` }"
      >
        <div class="carousel-overlay">
          <div class="carousel-content">
            <h2>{{ slide.title }}</h2>
            <p>{{ slide.description }}</p>
            <div class="cta-row">
              <button class="btn primary" @click="goToLogin">Entrar</button>
              <button class="btn ghost" @click="goToRegister">Criar conta</button>
            </div>
          </div>
        </div>
      </div>

      <div class="carousel-dots">
        <div
          v-for="(slide, index) in slides"
          :key="`dot-${index}`"
          :class="['dot', { active: currentSlide === index }]"
          @click="currentSlide = index"
        ></div>
      </div>

      <div class="carousel-controls">
        <button @click="prevSlide">‹</button>
        <button @click="nextSlide">›</button>
      </div>
    </div>

    <div class="container">
      <section id="features" class="features">
        <div class="feature-card">
          <h3>📚 Acervo Completo</h3>
          <p>Explore nossa vasta coleção de livros organizados por categoria, autor e gênero.</p>
        </div>
        <div class="feature-card">
          <h3>🔍 Busca Avançada</h3>
          <p>Encontre rapidamente o livro que procura com nossa ferramenta de busca inteligente.</p>
        </div>
        <div class="feature-card">
          <h3>⭐ Avaliações</h3>
          <p>Leia e compartilhe avaliações para descobrir suas próximas leituras favoritas.</p>
        </div>
        <div class="feature-card">
          <h3>👤 Perfil Pessoal</h3>
          <p>Personalize seu perfil com foto e descrição, gerencie seus empréstimos ativos e defina seu livro favorito.</p>
        </div>
      </section>

      <section id="highlights" class="highlights">
        <div class="section-head">
          <h3>Livros em destaque</h3>
          <p>Confira algumas obras do nosso acervo</p>
        </div>

        <div class="cards">
          <div v-for="book in featuredBooks" :key="book.id" class="card" @click="goToBook(book.id)">
            <div class="thumb">
              <img :src="book.photo" :alt="book.title" />
            </div>
            <div class="card-body">
              <h4 :title="book.title">{{ book.title }}</h4>
              <p class="author">{{ book.author.name }}</p>
              <p class="desc">{{ book.description || 'Descrição não disponível' }}</p>
            </div>
          </div>
        </div>

        <div class="view-all-container">
          <button class="view-all-button" @click="goToApp">📚 Ver Todos os Livros e Autores</button>
        </div>
      </section>

      <section class="testimonials">
        <div class="section-head">
          <h3>O que dizem sobre o PedBook</h3>
        </div>
        <div class="quotes">
          <blockquote v-for="(testimonial, index) in testimonials" :key="index">
            "{{ testimonial.text }}"
            <span>— {{ testimonial.author }}</span>
          </blockquote>
        </div>
      </section>
    </div>

    <footer id="contato" class="home-footer">
      <div class="footer-content">
        <div>
          <h4>Contato</h4>
          <ul class="contact-list">
            <li><strong>Telefone:</strong> (14) 91234-5678</li>
            <li><strong>E-mail:</strong> admin@pedbook.com.br</li>
          </ul>
        </div>
        <div>
          <h4>Endereço</h4>
          <p>
            PedBook Tecnologia Ltda.<br />R. Pedro Alpino, 401 - Mirante<br />Marília - SP, 17525-901
          </p>
        </div>
        <div>
          <h4>Sobre</h4>
          <p>Plataforma de biblioteca digital desenvolvida com Laravel + Vue.js.</p>
        </div>
        <div>
          <h4>Links</h4>
          <ul class="footer-links">
            <li><a href="#features" @click.prevent="scrollToFeatures">Funcionalidades</a></li>
            <li><a href="#contato" @click.prevent="scrollToContato">Contato</a></li>
            <li><a href="/login">Entrar</a></li>
            <li><a href="/register">Criar conta</a></li>
          </ul>
        </div>
      </div>
      <p class="copyright">© 2025 PedBook - Todos os direitos reservados</p>
    </footer>
  </div>
</template>

<script>
export default {
  name: 'HomePage',
  data() {
    return {
      currentSlide: 0,
      slideTimer: null,
      slides: [
        {
          title: 'Bem-vindo ao PedBook',
          subtitle: 'Descubra, alugue e compre livros de forma simples e rápida.',
          image: '/assets/home/slide-1.jpg',
        },
        {
          title: 'Explore novos autores',
          subtitle: 'Conheça autores incríveis e suas obras.',
          image: '/assets/home/slide-2.jpg',
        },
        {
          title: 'Sua biblioteca, do seu jeito',
          subtitle: 'Monte sua lista de favoritos e carrinho em poucos cliques.',
          image: '/assets/home/slide-3.jpg',
        },
      ],
      featuredBooks: [],
      testimonials: [
        {
          text: 'Interface linda e muito fácil de usar. Encontrei meus livros favoritos rapidinho.',
          author: 'Ana, leitora',
        },
        {
          text: 'Perfeito para organizar empréstimos e acompanhar avaliações da equipe.',
          author: 'Júlio, bibliotecário',
        },
        {
          text: 'Catálogo diverso e navegação fluida. Recomendo demais.',
          author: 'Carla, professora',
        },
      ],
    };
  },
  mounted() {
    this.loadFeaturedBooks();
    this.slideTimer = setInterval(() => this.nextSlide(), 5000);
  },
  beforeDestroy() {
    if (this.slideTimer) clearInterval(this.slideTimer);
  },
  methods: {
    async loadFeaturedBooks() {
      try {
        const res = await fetch('/graphql', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            query:
              'query Books($all: Boolean) { books(all: $all) { id title description photo author { id name } } }',
            variables: { all: true },
          }),
        });

        const payload = await res.json();
        const data = payload && payload.data ? payload.data : {};
        const books = data && Array.isArray(data.books) ? data.books : [];
        this.featuredBooks = books.slice(0, 6);
      } catch (e) {
        this.featuredBooks = [];
      }
    },
    nextSlide() {
      this.currentSlide = (this.currentSlide + 1) % this.slides.length;
    },
    prevSlide() {
      this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
    },
    goToApp() {
      window.location.href = '/app#books';
    },
    goToLogin() {
      window.location.href = '/login';
    },
    goToRegister() {
      window.location.href = '/register';
    },
    goToBook(id) {
      if (!id) return;
      window.location.href = `/app#book/${id}`;
    },
    scrollToTop() {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    },
    scrollToHighlights() {
      const el = document.getElementById('highlights');
      if (el) el.scrollIntoView({ behavior: 'smooth' });
    },
    scrollToFeatures() {
      const el = document.getElementById('features');
      if (el) el.scrollIntoView({ behavior: 'smooth' });
    },
    scrollToContato() {
      const el = document.getElementById('contato');
      if (el) el.scrollIntoView({ behavior: 'smooth' });
    },
  },
};
</script>

<style scoped>
  .home-root {
    min-height: 100vh;
    background: #0b122b;
  }
</style>
