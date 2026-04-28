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
         sidebarTitle.addEventListener('click', toggleSidebarMenu);
    }
    const handleResize = () => {
         if (window.innerWidth > mobileBreakpoint) {
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