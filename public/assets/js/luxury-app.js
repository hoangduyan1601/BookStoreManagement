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

    // 2. KHỞI TẠO ANIMATION CƠ BẢN CHO TRANG ĐẦU TIÊN
    function initPageAnimations() {
        if (typeof gsap === 'undefined') return;

        // Reset trạng thái trước khi animate để tránh bị kẹt
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
                clearProps: "opacity,visibility,transform" // Chỉ xóa các thuộc tính GSAP tác động, giữ lại background-image
            });
        }
        
        if(document.querySelector('.product-info-panel')) {
            gsap.from('.product-info-panel', { x: 50, opacity: 0, duration: 1, ease: "power3.out", delay: 0.2 });
        }
    }

    initPageAnimations();

    // 3. BARBA.JS - 3D CUBE TRANSITION SPA
    if (typeof barba !== 'undefined') {
        barba.init({
            sync: true,
            transitions: [{
                name: '3d-cube-transition',
                leave(data) {
                    const done = this.async();
                    gsap.to('.barba-transition-layer', { opacity: 1, duration: 0.4 });
                    gsap.to(data.current.container, {
                        scale: 0.8,
                        rotationY: -90,
                        opacity: 0,
                        transformOrigin: "right center",
                        duration: 0.8,
                        ease: "power3.inOut",
                        onComplete: done
                    });
                },
                enter(data) {
                    gsap.set(data.next.container, {
                        position: 'absolute',
                        top: 0,
                        left: 0,
                        width: '100%',
                        scale: 0.8,
                        rotationY: 90,
                        transformOrigin: "left center",
                        opacity: 0
                    });
                    gsap.to(data.next.container, {
                        scale: 1,
                        rotationY: 0,
                        opacity: 1,
                        duration: 0.8,
                        ease: "power3.inOut",
                        onComplete: () => {
                            gsap.set(data.next.container, { clearProps: "all" });
                            gsap.to('.barba-transition-layer', { opacity: 0, duration: 0.4 });
                            if (typeof ScrollTrigger !== 'undefined') {
                                ScrollTrigger.getAll().forEach(t => t.kill());
                            }
                            initPageAnimations();
                            
                            // Re-execute scripts in the new container
                            const scripts = data.next.container.querySelectorAll('script');
                            scripts.forEach(script => {
                                const newScript = document.createElement('script');
                                Array.from(script.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                                newScript.appendChild(document.createTextNode(script.innerHTML));
                                script.parentNode.replaceChild(newScript, script);
                            });
                        }
                    });
                }
            }]
        });

        barba.hooks.after((data) => {
            const nextHtml = data.next.html;
            const match = nextHtml.match(/<title>(.*?)<\/title>/);
            if (match) document.title = match[1];
        });
    }
});