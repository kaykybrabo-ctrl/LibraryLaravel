export default {
  methods: {
    thumb(url, w = 100, h = 100, kind = 'book') {
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

        const knownMissingCloudinary = new Set([
          'pedbook/books/book-magicians',
          'pedbook/books/book-brazil-biography',
          'pedbook/books/book-mundos-paralelos',
          'pedbook/books/book-echoes-time',
          'pedbook/books/book-caminho-estrelas',
        ]);

        if (kind === 'book' && knownMissingCloudinary.has(v)) {
          return fallback;
        }

        if (kind === 'book') {
          for (const id of knownMissingCloudinary) {
            if (v.includes(id)) {
              return fallback;
            }
          }
        }
        const isBarePublicId = v.startsWith('pedbook/');
        if (isBarePublicId) {
          const base = 'https://res.cloudinary.com/ddfgsoh5g/image/upload/';
          const withBase = base + v;
          return this.thumb(withBase, w, h, kind);
        }

        if (v.startsWith('http://') || v.startsWith('https://')) {
          const isCloudinary = v.includes('res.cloudinary.com/') && (v.includes('/image/upload/') || v.includes('/upload/'));
          if (!isCloudinary) return v;

          let normalized = v;

          const defaultPublicIdByKind = {
            user: 'pedbook/profiles/default-user.svg',
            author: 'pedbook/profiles/default-author.svg',
            book: 'pedbook/books/default-book.svg',
          };
          const defaultPublicId = defaultPublicIdByKind[kind] || defaultPublicIdByKind.book;
          const defaultTransform = `d_${String(defaultPublicId).replace(/\//g, ':')}`;

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

    goToPage(page) {
      this.routePage = page;
      if (typeof window !== 'undefined') {
        window.location.hash = page;
      }
    },

    goBack() {
      this.goToPage('books');
    },
  },
};
