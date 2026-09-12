import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const navigationToggle = document.querySelector('[data-nav-toggle]');
    const navigation = document.querySelector('[data-site-navigation]');
    const backdrop = document.querySelector('[data-nav-backdrop]');
    const navigationIcon = navigationToggle?.querySelector('.fa-bars, .fa-xmark');
    const mobileViewport = window.matchMedia('(max-width: 768px)');

    const setNavOpen = (isOpen) => {
        if (!navigation || !navigationToggle) {
            return;
        }

        navigation.classList.toggle('is-open', isOpen);
        navigationToggle.setAttribute('aria-expanded', String(isOpen));

        if (navigationToggle.dataset.navOpenLabel && navigationToggle.dataset.navCloseLabel) {
            navigationToggle.setAttribute(
                'aria-label',
                isOpen ? navigationToggle.dataset.navCloseLabel : navigationToggle.dataset.navOpenLabel,
            );
        }

        if (navigationIcon) {
            navigationIcon.classList.toggle('fa-bars', !isOpen);
            navigationIcon.classList.toggle('fa-xmark', isOpen);
        }

        if (backdrop) {
            backdrop.hidden = !isOpen;
            backdrop.classList.toggle('is-visible', isOpen);
        }

        document.body.style.overflow = isOpen ? 'hidden' : '';
    };

    if (navigationToggle && navigation) {
        navigationToggle.addEventListener('click', () => {
            setNavOpen(!navigation.classList.contains('is-open'));
        });

        navigation.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                setNavOpen(false);
            });
        });

        if (backdrop) {
            backdrop.addEventListener('click', () => {
                setNavOpen(false);
            });
        }

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && navigation.classList.contains('is-open')) {
                setNavOpen(false);
            }
        });

        mobileViewport.addEventListener('change', (event) => {
            if (!event.matches) {
                setNavOpen(false);
            }
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

    document.querySelectorAll('[data-track-dependent-grade-level]').forEach((gradeLevelSelect) => {
        const form = gradeLevelSelect.closest('form');
        const trackSelect = form?.querySelector('[data-track-selector]');
        const warning = form?.querySelector('[data-track-mismatch-message]');

        if (!trackSelect) {
            return;
        }

        const selectedGradeTrack = () => gradeLevelSelect.selectedOptions[0]?.dataset.track;
        const hideWarning = () => {
            if (warning) {
                warning.hidden = true;
            }
        };
        const showMismatchWarning = () => {
            if (warning) {
                warning.hidden = false;
            }
        };
        const gradeMatchesTrack = () => {
            const gradeTrack = selectedGradeTrack();

            return !gradeTrack || !trackSelect.value || gradeTrack === 'both' || gradeTrack === trackSelect.value;
        };

        gradeLevelSelect.addEventListener('change', () => {
            const gradeTrack = selectedGradeTrack();

            if (!gradeTrack) {
                hideWarning();
                return;
            }

            if (!trackSelect.value) {
                trackSelect.value = gradeTrack;
                hideWarning();
                return;
            }

            if (!gradeMatchesTrack()) {
                gradeLevelSelect.value = '';
                showMismatchWarning();
                return;
            }

            hideWarning();
        });

        trackSelect.addEventListener('change', () => {
            if (!gradeMatchesTrack()) {
                gradeLevelSelect.value = '';
                showMismatchWarning();
                return;
            }

            hideWarning();
        });
    });
});
