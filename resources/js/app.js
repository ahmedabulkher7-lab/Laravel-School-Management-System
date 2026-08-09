import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const navigationToggle = document.querySelector('[data-nav-toggle]');
    const navigation = document.querySelector('[data-site-navigation]');

    if (navigationToggle && navigation) {
        navigationToggle.addEventListener('click', () => {
            const isOpen = navigation.classList.toggle('is-open');
            navigationToggle.setAttribute('aria-expanded', String(isOpen));
        });

        navigation.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                navigation.classList.remove('is-open');
                navigationToggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    const revealElements = document.querySelectorAll('[data-reveal]');
    if ('IntersectionObserver' in window && revealElements.length) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        revealElements.forEach((element) => observer.observe(element));
    } else {
        revealElements.forEach((element) => element.classList.add('is-visible'));
    }

    const counters = document.querySelectorAll('[data-counter]');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const showCounterValue = (counter) => {
        counter.textContent = `${counter.dataset.counter}${counter.dataset.suffix ?? ''}`;
    };

    const animateCounter = (counter) => {
        const target = Number.parseFloat(counter.dataset.counter);
        const suffix = counter.dataset.suffix ?? '';
        const duration = 1050;
        const startedAt = performance.now();
        const precision = Number.isInteger(target) ? 0 : 1;

        const tick = (now) => {
            const progress = Math.min((now - startedAt) / duration, 1);
            const easedProgress = 1 - ((1 - progress) ** 3);
            counter.textContent = `${(target * easedProgress).toFixed(precision)}${suffix}`;

            if (progress < 1) {
                requestAnimationFrame(tick);
            }
        };

        requestAnimationFrame(tick);
    };

    if (counters.length) {
        if (reduceMotion || !('IntersectionObserver' in window)) {
            counters.forEach(showCounterValue);
        } else {
            const counterObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        animateCounter(entry.target);
                        counterObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.7 });

            counters.forEach((counter) => counterObserver.observe(counter));
        }
    }
});
