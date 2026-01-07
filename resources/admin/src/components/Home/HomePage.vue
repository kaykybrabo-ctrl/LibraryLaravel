<template>
  <div class="home-root">
    <div class="home-header">
      <div class="header-container">
        <div class="brand" @click="scrollToTop" role="button" tabindex="0">
          <span class="logo"></span>
          <h1>PedBook</h1>
        </div>
        <nav class="home-nav">
          <a href="/app#books" class="nav-link">{{ $t('navigation.books') }}</a>
          <a href="/app#authors" class="nav-link">{{ $t('navigation.authors') }}</a>
          <a href="#highlights" class="nav-link" @click.prevent="scrollToHighlights">{{ $t('home.highlightsNav') }}</a>
          <a href="/login" class="nav-link login-btn">{{ $t('auth.login') }}</a>
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
            <h2>{{ $t(slide.titleKey) }}</h2>
            <p>{{ $t(slide.subtitleKey) }}</p>
            <div class="cta-row">
              <button class="btn primary" @click="goToLogin">{{ $t('auth.login') }}</button>
              <button class="btn ghost" @click="goToRegister">{{ $t('auth.register') }}</button>
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
          <h3>{{ $t('home.features.collectionTitle') }}</h3>
          <p>{{ $t('home.features.collectionDescription') }}</p>
        </div>
        <div class="feature-card">
          <h3>{{ $t('home.features.searchTitle') }}</h3>
          <p>{{ $t('home.features.searchDescription') }}</p>
        </div>
        <div class="feature-card">
          <h3>{{ $t('home.features.reviewsTitle') }}</h3>
          <p>{{ $t('home.features.reviewsDescription') }}</p>
        </div>
        <div class="feature-card">
          <h3>{{ $t('home.features.profileTitle') }}</h3>
          <p>{{ $t('home.features.profileDescription') }}</p>
        </div>
      </section>

      <section id="highlights" class="highlights">
        <div class="section-head">
          <h3>{{ $t('home.highlightsTitle') }}</h3>
          <p>{{ $t('home.highlightsSubtitle') }}</p>
        </div>

        <div class="cards">
          <div v-for="book in featuredBooks" :key="book.id" class="card" @click="goToBook(book.id)">
            <div class="thumb">
              <img
                :src="thumb(book.photo, 600, 360, 'book')"
                :alt="book.title"
                @error="$event.target.src = thumb('', 600, 360, 'book')"
              />
            </div>
            <div class="card-body">
              <h4 :title="book.title">{{ book.title }}</h4>
              <p class="author">{{ book.author.name }}</p>
              <p class="desc">{{ book.description || $t('books.noDescription') }}</p>
            </div>
          </div>
        </div>

        <div class="view-all-container">
          <button class="view-all-button" @click="goToApp">{{ $t('home.viewAllButton') }}</button>
        </div>
      </section>

      <section class="testimonials">
        <div class="section-head">
          <h3>{{ $t('home.testimonialsTitle') }}</h3>
        </div>
        <div class="quotes">
          <blockquote v-for="(testimonial, index) in testimonials" :key="index">
            "{{ $t(testimonial.textKey) }}"
            <span>— {{ $t(testimonial.authorKey) }}</span>
          </blockquote>
        </div>
      </section>
    </div>

    <footer id="contato" class="home-footer">
      <div class="footer-content">
        <div>
          <h4>{{ $t('home.footer.contact') }}</h4>
          <ul class="contact-list">
            <li><strong>{{ $t('home.footer.phone') }}:</strong> (14) 91234-5678</li>
            <li><strong>{{ $t('home.footer.email') }}:</strong> admin@pedbook.com.br</li>
          </ul>
        </div>
        <div>
          <h4>{{ $t('home.footer.address') }}</h4>
          <p>
            {{ $t('home.footer.addressLine1') }}<br />{{ $t('home.footer.addressLine2') }}<br />{{ $t('home.footer.addressLine3') }}
          </p>
        </div>
        <div>
          <h4>{{ $t('home.footer.about') }}</h4>
          <p>{{ $t('home.footer.aboutText') }}</p>
        </div>
        <div>
          <h4>{{ $t('home.footer.links') }}</h4>
          <ul class="footer-links">
            <li><a href="#features" @click.prevent="scrollToFeatures">{{ $t('home.footer.featuresLink') }}</a></li>
            <li><a href="#contato" @click.prevent="scrollToContato">{{ $t('home.footer.contactLink') }}</a></li>
            <li><a href="/login">{{ $t('auth.login') }}</a></li>
            <li><a href="/register">{{ $t('auth.register') }}</a></li>
          </ul>
        </div>
      </div>
      <p class="copyright">{{ $t('home.footer.copyright') }}</p>
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
          titleKey: 'home.carousel.slide1.title',
          subtitleKey: 'home.carousel.slide1.subtitle',
          image: '/assets/home/slide-1.jpg',
        },
        {
          titleKey: 'home.carousel.slide2.title',
          subtitleKey: 'home.carousel.slide2.subtitle',
          image: '/assets/home/slide-2.jpg',
        },
        {
          titleKey: 'home.carousel.slide3.title',
          subtitleKey: 'home.carousel.slide3.subtitle',
          image: '/assets/home/slide-3.jpg',
        },
      ],
      featuredBooks: [],
      testimonials: [
        {
          textKey: 'home.testimonials.t1.text',
          authorKey: 'home.testimonials.t1.author',
        },
        {
          textKey: 'home.testimonials.t2.text',
          authorKey: 'home.testimonials.t2.author',
        },
        {
          textKey: 'home.testimonials.t3.text',
          authorKey: 'home.testimonials.t3.author',
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
    thumb(url, w = 600, h = 360, kind = 'book') {
      try {
        const clean = (v) => String(v || '').trim();
        const v = clean(url);

        const defaults = {
          user: 'https://res.cloudinary.com/ddfgsoh5g/image/upload/v1761062930/pedbook/profiles/default-user.svg',
          author: 'https://res.cloudinary.com/ddfgsoh5g/image/upload/v1761062934/pedbook/profiles/default-author.svg',
          book: 'https://res.cloudinary.com/ddfgsoh5g/image/upload/v1761062932/pedbook/books/default-book.svg',
        };
        const fallback = defaults[kind] || defaults.book;

        const isMissing = !v || v === 'null' || v === 'undefined';
        if (isMissing) return fallback;
        if (v.startsWith('/')) return v;
        if (v.startsWith('assets/')) return `/${v}`;

        if (v.startsWith('pedbook/')) {
          const base = 'https://res.cloudinary.com/ddfgsoh5g/image/upload/';
          return this.thumb(base + v, w, h, kind);
        }

        if (v.startsWith('http://') || v.startsWith('https://')) {
          const isCloudinary = v.includes('res.cloudinary.com/') && (v.includes('/image/upload/') || v.includes('/upload/'));
          if (!isCloudinary) return v;

          const defaultPublicIdByKind = {
            user: 'pedbook/profiles/default-user.svg',
            author: 'pedbook/profiles/default-author.svg',
            book: 'pedbook/books/default-book.svg',
          };
          const defaultPublicId = defaultPublicIdByKind[kind] || defaultPublicIdByKind.book;
          const defaultTransform = `d_${String(defaultPublicId).replace(/\//g, ':')}`;

          let normalized = v;
          const uploadMarker = normalized.includes('/image/upload/') ? '/image/upload/' : '/upload/';
          const after = normalized.split(uploadMarker)[1] || '';
          const first = after.split('/')[0] || '';
          const hasTransform = first.includes('_') || first.includes(',') || first.startsWith('c_') || first.startsWith('w_') || first.startsWith('q_') || first.startsWith('f_');

          if (w && h && !hasTransform) {
            normalized = normalized.replace(
              uploadMarker,
              `${uploadMarker}c_fill,w_${Math.round(w)},h_${Math.round(h)},q_auto,f_auto,${defaultTransform}/`
            );
          }

          return normalized;
        }

        return fallback;
      } catch (e) {
        return 'https://res.cloudinary.com/ddfgsoh5g/image/upload/v1761062932/pedbook/books/default-book.svg';
      }
    },
    async loadFeaturedBooks() {
      try {
        const res = await fetch('/graphql', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            query:
              'query BooksPage($per: Int!, $page: Int) { booksPage(per_page: $per, page: $page) { data { id title description photo author { id name } } } }',
            variables: { per: 6, page: 1 },
          }),
        });

        const payload = await res.json();
        const data = payload && payload.data ? payload.data : {};
        const page = data && data.booksPage ? data.booksPage : null;
        const books = page && Array.isArray(page.data) ? page.data : [];
        this.featuredBooks = books;
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
