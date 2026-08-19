document.addEventListener('DOMContentLoaded', () => {
    const hamburgerToggle = document.querySelector('.hamburger-menu-toggle');
    const nav = document.querySelector('nav');
    const sidebarTitle = document.querySelector('.sidebar-menu-title');
    const sidebarList = document.querySelector('.sidebar-menu-list');
    const backToTopButton = document.getElementById('back-to-top');
    const mobileBreakpoint = 768;

    if (hamburgerToggle && nav) {
        hamburgerToggle.addEventListener('click', () => {
            nav.classList.toggle('menu-open');
            hamburgerToggle.classList.toggle('is-active');
        });
    }

    const toggleSidebarMenu = () => {
        if (window.innerWidth <= mobileBreakpoint) {
            sidebarList.classList.toggle('sidebar-open');
            sidebarTitle.classList.toggle('sidebar-open');
        }
    };

    if (sidebarTitle && sidebarList) {
        let touchStartY = 0;
        let touchStartX = 0;
        let isScrolling = false;

        sidebarTitle.addEventListener('touchstart', (e) => {
            touchStartY = e.touches[0].clientY;
            touchStartX = e.touches[0].clientX;
            isScrolling = false;
        }, { passive: true });

        sidebarTitle.addEventListener('touchmove', (e) => {
            const deltaY = Math.abs(e.touches[0].clientY - touchStartY);
            const deltaX = Math.abs(e.touches[0].clientX - touchStartX);
            if (deltaY > 10 || deltaX > 10) {
                isScrolling = true;
            }
        }, { passive: true });

        sidebarTitle.addEventListener('touchend', (e) => {
            if (!isScrolling) {
                e.preventDefault();
                toggleSidebarMenu();
            }
        });

        sidebarTitle.addEventListener('click', (e) => {
            if ('ontouchstart' in window) return;
            toggleSidebarMenu();
        });
    }

    let lastWidth = window.innerWidth;
    const handleResize = () => {
        const currentWidth = window.innerWidth;
        if (currentWidth === lastWidth) return;
        lastWidth = currentWidth;

        if (currentWidth > mobileBreakpoint) {
            if (nav) nav.classList.remove('menu-open');
            if (hamburgerToggle) hamburgerToggle.classList.remove('is-active');
            if (sidebarList) sidebarList.classList.add('sidebar-open');
            if (sidebarTitle) sidebarTitle.classList.add('sidebar-open');
        } else {
            if (nav) nav.classList.remove('menu-open');
            if (sidebarList) sidebarList.classList.remove('sidebar-open');
        }
    };

    window.addEventListener('resize', handleResize);

    // === Заказ звонка (модалка) ===
    const callbackModal = document.getElementById('callback-modal');
    const callbackForm = document.getElementById('callback-form');
    const callbackPhone = document.getElementById('callback-phone');
    const callbackError = document.getElementById('callback-error');
    const callbackStatus = document.getElementById('callback-status');

    const openCallback = () => {
        if (!callbackModal) return;
        callbackModal.hidden = false;
        document.body.classList.add('callback-modal-open');
        if (callbackPhone) callbackPhone.focus();
    };

    const closeCallback = () => {
        if (!callbackModal) return;
        callbackModal.hidden = true;
        document.body.classList.remove('callback-modal-open');
    };

    document.querySelectorAll('[data-callback-open]').forEach((btn) => {
        btn.addEventListener('click', openCallback);
    });
    if (callbackModal) {
        callbackModal.querySelectorAll('[data-callback-close]').forEach((el) => {
            el.addEventListener('click', closeCallback);
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !callbackModal.hidden) closeCallback();
        });
    }

    const showCallbackError = (message) => {
        if (!callbackError) return;
        callbackError.textContent = message;
        callbackError.hidden = false;
        if (callbackPhone) callbackPhone.focus();
    };

    const clearCallbackError = () => {
        if (!callbackError) return;
        callbackError.hidden = true;
        callbackError.textContent = '';
    };

    if (callbackForm) {
        callbackForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            clearCallbackError();

            const phone = callbackPhone ? callbackPhone.value.trim() : '';
            const digits = phone.replace(/\D/g, '');
            if (digits.length < 10 || digits.length > 15) {
                showCallbackError('Введите номер телефона (10–15 цифр).');
                return;
            }

            const submitBtn = callbackForm.querySelector('button[type="submit"]');
            const companyInput = callbackForm.querySelector('.callback-hp');
            const data = {
                phone,
                company: companyInput ? companyInput.value : ''
            };

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Отправляем…';
            }

            try {
                const res = await fetch('/callback/', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await res.json().catch(() => ({}));

                if (res.ok && result.ok) {
                    if (callbackStatus) {
                        callbackStatus.textContent = 'Спасибо! Мы перезвоним в рабочее время (пн–пт 9:00–18:00).';
                        callbackStatus.hidden = false;
                    }
                    callbackForm.hidden = true;
                    if (window.ym) window.ym(21639061, 'reachGoal', 'callback_submit');
                    setTimeout(closeCallback, 3000);
                } else if (res.status === 429) {
                    showCallbackError('Слишком много заявок. Попробуйте через 2 минуты.');
                } else {
                    showCallbackError('Не удалось отправить. Позвоните нам: +7 (342) 20-99-800.');
                }
            } catch (err) {
                showCallbackError('Нет связи с сервером. Позвоните нам: +7 (342) 20-99-800.');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Жду звонка';
                }
            }
        });

        callbackPhone.addEventListener('input', clearCallbackError);
    }

    if (backToTopButton) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 200) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });
        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    handleResize();
});