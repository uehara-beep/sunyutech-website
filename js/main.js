/**
 * サンユウテック株式会社 - メインJavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
  // Mobile Menu Toggle
  initMobileMenu();

  // Smooth Scroll
  initSmoothScroll();

  // Fade In Animation
  initFadeInAnimation();

  // Form Validation
  initFormValidation();

  // Header Scroll Effect
  initHeaderScroll();

  // Count Up Animation
  initCountUpAnimation();

  // Typing Animation
  initTypingAnimation();
});

/**
 * モバイルメニューの初期化
 */
function initMobileMenu() {
  const menuToggle = document.querySelector('.menu-toggle');
  const navMenu = document.querySelector('.nav-menu');

  if (menuToggle && navMenu) {
    menuToggle.addEventListener('click', function() {
      navMenu.classList.toggle('active');
      menuToggle.classList.toggle('active');

      // アクセシビリティ対応
      const isExpanded = navMenu.classList.contains('active');
      menuToggle.setAttribute('aria-expanded', isExpanded);
    });

    // メニューリンクをクリックしたらメニューを閉じる
    const navLinks = navMenu.querySelectorAll('a');
    navLinks.forEach(function(link) {
      link.addEventListener('click', function() {
        navMenu.classList.remove('active');
        menuToggle.classList.remove('active');
      });
    });

    // メニュー外をクリックしたらメニューを閉じる
    document.addEventListener('click', function(e) {
      if (!menuToggle.contains(e.target) && !navMenu.contains(e.target)) {
        navMenu.classList.remove('active');
        menuToggle.classList.remove('active');
      }
    });
  }
}

/**
 * スムーススクロールの初期化
 */
function initSmoothScroll() {
  const links = document.querySelectorAll('a[href^="#"]');

  links.forEach(function(link) {
    link.addEventListener('click', function(e) {
      const href = this.getAttribute('href');

      if (href === '#') return;

      const target = document.querySelector(href);

      if (target) {
        e.preventDefault();

        const headerHeight = document.querySelector('.header').offsetHeight;
        const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight - 20;

        window.scrollTo({
          top: targetPosition,
          behavior: 'smooth'
        });
      }
    });
  });
}


/**
 * フォームバリデーションの初期化
 */
function initFormValidation() {
  const contactForm = document.getElementById('contactForm');

  if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
      e.preventDefault();

      // フォームデータの取得
      const formData = new FormData(contactForm);
      const data = {};

      formData.forEach(function(value, key) {
        data[key] = value;
      });

      // バリデーション
      let isValid = true;
      const errors = [];

      if (!data.company || data.company.trim() === '') {
        errors.push('会社名・団体名を入力してください');
        isValid = false;
      }

      if (!data.name || data.name.trim() === '') {
        errors.push('お名前を入力してください');
        isValid = false;
      }

      if (!data.email || data.email.trim() === '') {
        errors.push('メールアドレスを入力してください');
        isValid = false;
      } else if (!isValidEmail(data.email)) {
        errors.push('有効なメールアドレスを入力してください');
        isValid = false;
      }

      if (!data.tel || data.tel.trim() === '') {
        errors.push('電話番号を入力してください');
        isValid = false;
      }

      if (!data.message || data.message.trim() === '') {
        errors.push('お問い合わせ内容を入力してください');
        isValid = false;
      }

      if (!isValid) {
        alert('入力内容をご確認ください。\n\n' + errors.join('\n'));
        return;
      }

      // フォーム送信処理（実際の実装時はサーバーサイドの処理が必要）
      // ここではデモとして確認メッセージを表示
      alert('お問い合わせありがとうございます。\n\n送信内容を確認後、担当者よりご連絡いたします。\n通常2営業日以内にご返答いたします。');

      // フォームをリセット
      contactForm.reset();

      // 実際の実装では、以下のようにサーバーに送信
      // fetch('/api/contact', {
      //   method: 'POST',
      //   headers: {
      //     'Content-Type': 'application/json',
      //   },
      //   body: JSON.stringify(data)
      // })
      // .then(response => response.json())
      // .then(result => {
      //   // 成功処理
      // })
      // .catch(error => {
      //   // エラー処理
      // });
    });
  }
}

/**
 * メールアドレスのバリデーション
 */
function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

/**
 * ヘッダースクロールエフェクトの初期化
 */
function initHeaderScroll() {
  const header = document.querySelector('.header');

  if (!header) return;

  let lastScrollTop = 0;
  const scrollThreshold = 100;

  window.addEventListener('scroll', function() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    // スクロール量に応じてヘッダーにシャドウを追加
    if (scrollTop > scrollThreshold) {
      header.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.15)';
    } else {
      header.style.boxShadow = '0 2px 15px rgba(0, 0, 0, 0.1)';
    }

    lastScrollTop = scrollTop;
  });
}

/**
 * 電話番号リンクのスマートフォン対応
 * PCでは電話発信を無効化
 */
function handleTelLinks() {
  const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

  if (!isMobile) {
    const telLinks = document.querySelectorAll('a[href^="tel:"]');
    telLinks.forEach(function(link) {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        // PCの場合は電話番号をクリップボードにコピー
        const telNumber = this.getAttribute('href').replace('tel:', '');
        if (navigator.clipboard) {
          navigator.clipboard.writeText(telNumber).then(function() {
            alert('電話番号をコピーしました: ' + telNumber);
          });
        }
      });
    });
  }
}

// 電話番号リンクの処理を実行
handleTelLinks();

/**
 * カウントアップアニメーションの初期化
 */
function initCountUpAnimation() {
  const countElements = document.querySelectorAll('.count-up');

  if (countElements.length === 0) return;

  const observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (entry.isIntersecting) {
        const element = entry.target;
        const target = parseInt(element.getAttribute('data-count'), 10);
        const duration = 2000; // アニメーション時間（ミリ秒）
        const increment = target / (duration / 16); // 約60fpsで計算

        let current = 0;
        const updateCount = function() {
          current += increment;
          if (current < target) {
            element.textContent = Math.floor(current);
            requestAnimationFrame(updateCount);
          } else {
            element.textContent = target;
          }
        };

        updateCount();
        observer.unobserve(element);
      }
    });
  }, {
    threshold: 0.5
  });

  countElements.forEach(function(element) {
    observer.observe(element);
  });
}

/**
 * タイピングアニメーションの初期化
 */
function initTypingAnimation() {
  const typingElements = document.querySelectorAll('.typing-effect');

  if (typingElements.length === 0) return;

  typingElements.forEach(function(element) {
    const text = element.getAttribute('data-text') || element.textContent;
    const speed = parseInt(element.getAttribute('data-speed'), 10) || 100;

    element.textContent = '';
    element.style.visibility = 'visible';

    let charIndex = 0;

    function typeChar() {
      if (charIndex < text.length) {
        element.textContent += text.charAt(charIndex);
        charIndex++;
        setTimeout(typeChar, speed);
      } else {
        element.classList.add('typing-complete');
      }
    }

    // ページ読み込み後に少し遅延してから開始
    setTimeout(typeChar, 500);
  });
}

/**
 * 汎用フェードインアニメーション（複数クラス対応）
 */
function initFadeInAnimation() {
  const fadeSelectors = '.fade-in, .fade-in-left, .fade-in-right, .scale-in';
  const fadeElements = document.querySelectorAll(fadeSelectors);

  if (fadeElements.length === 0) return;

  const observer = new IntersectionObserver(function(entries) {
    entries.forEach(function(entry) {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  });

  fadeElements.forEach(function(element) {
    observer.observe(element);
  });
}
