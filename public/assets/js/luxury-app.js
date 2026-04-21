document.addEventListener("DOMContentLoaded", () => {
    // Đăng ký ScrollTrigger
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);
    }

    // 1. SMART HEADER LOGIC
    let lastScroll = 0;
    const header = document.querySelector('.smart-header');
    
    if (header) {
        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            if (currentScroll <= 0) {
                header.classList.remove('hidden');
            } else if (currentScroll > lastScroll && currentScroll > 100) {
                header.classList.add('hidden');
            } else {
                header.classList.remove('hidden');
            }
            lastScroll = currentScroll;
        });
    }

    // 2. KHỞI TẠO ANIMATION CƠ BẢN
    function initPageAnimations() {
        if (typeof gsap === 'undefined') return;
        gsap.set('.product-card', { opacity: 1, filter: 'none' });
        if(document.querySelector('.hero-content')) {
            gsap.from('.hero-content', { y: 50, opacity: 0, duration: 1.5, ease: "power4.out", delay: 0.2 });
        }
        const bentoItems = document.querySelectorAll('.product-card, .bento-item');
        if(bentoItems.length > 0 && typeof ScrollTrigger !== 'undefined') {
            gsap.from(bentoItems, {
                scrollTrigger: {
                    trigger: bentoItems[0].parentElement,
                    start: "top 90%",
                    toggleActions: "play none none none"
                },
                y: 30,
                opacity: 0,
                stagger: 0.05,
                duration: 0.8,
                ease: "power2.out",
                clearProps: "all"
            });
        }
    }

    initPageAnimations();

    // 3. BARBA.JS - 3D CUBE TRANSITION SPA
    if (typeof barba !== 'undefined') {
        barba.init({
            sync: true,
            cacheIgnore: true, // Vô hiệu hóa cache để luôn lấy dữ liệu mới nhất
            prefetchIgnore: true,
            transitions: [{
                name: '3d-cube-transition',
                leave(data) {
                    const done = this.async();
                    gsap.to('.barba-transition-layer', { opacity: 1, duration: 0.3 });
                    gsap.to(data.current.container, {
                        scale: 0.9,
                        rotationY: -15,
                        opacity: 0,
                        duration: 0.5,
                        ease: "power2.inOut",
                        onComplete: done
                    });
                },
                enter(data) {
                    window.scrollTo(0, 0);

                    // Update body class for prank mode from the new page
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(data.next.html, 'text/html');
                    const newBodyClass = doc.body.className;
                    document.body.className = newBodyClass;

                    gsap.from(data.next.container, {
                        scale: 0.9,
                        rotationY: 15,
                        opacity: 0,
                        duration: 0.5,
                        ease: "power2.out",
                        onComplete: () => {
                            initPageAnimations();
                            updateHeaderBadges(data.next.html);
                        }
                    });

                    // Re-execute scripts
                    const scripts = data.next.container.querySelectorAll('script');
                    scripts.forEach(script => {
                        const newScript = document.createElement('script');
                        Array.from(script.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                        newScript.appendChild(document.createTextNode(script.innerHTML));
                        script.parentNode.replaceChild(newScript, script);
                    });
                }
            }]
        });

        // Hàm cập nhật Badge trên Header từ HTML mới nhận được
        function updateHeaderBadges(html) {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Cập nhật Badge Giỏ hàng
            const newCartBadge = doc.getElementById('cart-count-badge');
            const currentCartBadge = document.getElementById('cart-count-badge');
            if (newCartBadge && currentCartBadge) {
                currentCartBadge.innerText = newCartBadge.innerText;
                if (newCartBadge.classList.contains('d-none')) currentCartBadge.classList.add('d-none');
                else currentCartBadge.classList.remove('d-none');
            }

            // Cập nhật Badge Yêu thích
            const newFavBadge = doc.getElementById('fav-count-badge');
            const currentFavBadge = document.getElementById('fav-count-badge');
            if (newFavBadge && currentFavBadge) {
                currentFavBadge.innerText = newFavBadge.innerText;
                if (newFavBadge.classList.contains('d-none')) currentFavBadge.classList.add('d-none');
                else currentFavBadge.classList.remove('d-none');
            }
        }

        barba.hooks.after((data) => {
            const nextHtml = data.next.html;
            const match = nextHtml.match(/<title>(.*?)<\/title>/);
            if (match) document.title = match[1];
        });
    }
});
