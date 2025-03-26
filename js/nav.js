// 导航栏长度限制（添加防抖）
function initNavWidth() {
    const nav = document.querySelector('nav');
    let resizeTimer;
    
    const checkWidth = () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            const isOver = nav.offsetWidth > 1200;
            nav.style.overflowX = isOver ? 'hidden' : '';
            nav.style.maxWidth = isOver ? '1200px' : '';
        }, 100);
    };

    checkWidth();
    window.addEventListener('resize', checkWidth);
}

// 使用缓存的DOM查询
const DOM = {
    get bgNext() { return document.getElementById("bg-next") },
    get navSearchWrapper() { return document.querySelector(".nav-search-wrapper") },
    get searchbox() { return document.querySelector(".searchbox.js-toggle-search") },
    get divider() { return document.querySelector(".nav-search-divider") }
};

// 动画参数优化
const ANIMATION = {
    get easing() { return "cubic-bezier(0.34, 1.56, 0.64, 1)" },
    get duration() { return "0.6s" },
    get durationMs() { return 600 }
};

// 状态管理器优化（添加内存缓存）
const StateManager = {
    cache: null,
    init() {
        if (this.cache) return this.cache;
        try {
            const stored = sessionStorage.getItem("bgNextState");
            if (stored) {
                this.cache = JSON.parse(stored);
                return this.cache;
            }
            this.cache = {
                lastPageWasHome: false,
                isTransitioning: false,
                firstLoad: true,
                initialized: false
            };
            sessionStorage.setItem("bgNextState", JSON.stringify(this.cache));
            return this.cache;
        } catch (e) {
            console.warn('StateManager initialization failed:', e);
            return {
                lastPageWasHome: false,
                isTransitioning: false,
                firstLoad: true,
                initialized: false
            };
        }
    },
    getState() {
        return this.cache || JSON.parse(sessionStorage.getItem("bgNextState"));
    },
    setState(state) {
        this.cache = state;
        sessionStorage.setItem("bgNextState", JSON.stringify(state));
    },
    update(changes) {
        const currentState = this.getState();
        this.setState({...currentState, ...changes});
    }
};

// 统一样式应用函数
const applyStyles = (element, styles) => {
    if (!element) return;
    Object.entries(styles).forEach(([prop, value]) => {
        element.style[prop] = value;
    });
};

// 优化测量逻辑
const measureElementWidth = (element) => {
    const clone = element.cloneNode(true);
    clone.style.cssText = `
        position: absolute;
        visibility: hidden;
        white-space: nowrap;
        margin: 0;
        padding: 0;
    `;
    document.body.appendChild(clone);
    const width = clone.offsetWidth;
    document.body.removeChild(clone);
    return width;
};

// 设置动画过渡
const setTransitions = () => {
    const transitions = {
        transition: `all ${ANIMATION.duration} ${ANIMATION.easing}`
    };
    
    applyStyles(DOM.bgNext, transitions);
    applyStyles(DOM.navSearchWrapper, transitions);
    
    if (DOM.searchbox) {
        applyStyles(DOM.searchbox, { 
            transition: `transform ${ANIMATION.duration} ${ANIMATION.easing}`
        });
    }
    
    if (DOM.divider) {
        applyStyles(DOM.divider, {
            transition: !DOM.searchbox 
                ? `all ${ANIMATION.duration} ${ANIMATION.easing}`
                : `transform ${ANIMATION.duration} ${ANIMATION.easing}`
        });
    }
};

// 初始化元素状态优化
const initElementStates = (isEntering, bgNextWidth, initialWidth, isFirstLoad = false) => {
    applyStyles(DOM.navSearchWrapper, { width: `${initialWidth}px` });
    
    const bgNextStyles = {
        display: 'block',
        opacity: isEntering ? "0" : "1",
        transform: `translateX(${isEntering ? "20px" : "0"})`,
        transition: 'none'
    };
    applyStyles(DOM.bgNext, bgNextStyles);

    if (!DOM.searchbox && DOM.divider && isEntering && !isFirstLoad) {
        applyStyles(DOM.divider, {
            display: 'block',
            opacity: '0',
            transform: `translateX(${bgNextWidth}px)`,
            transition: 'none'
        });
    }
    
    if (isEntering && !isFirstLoad) {
        setInitialPositions(bgNextWidth);
    }
};

// 设置初始位置优化
const setInitialPositions = (bgNextWidth) => {
    const searchboxStyles = {
        transform: `translateX(${bgNextWidth}px)`,
        transition: 'none'
    };
    
    if (DOM.searchbox) {
        applyStyles(DOM.searchbox, searchboxStyles);
    }
    
    if (DOM.divider) {
        const dividerStyles = {
            transform: `translateX(${bgNextWidth}px)`,
            transition: 'none'
        };
        
        if (!DOM.searchbox) {
            dividerStyles.opacity = '0';
        }
        applyStyles(DOM.divider, dividerStyles);
    }
};

// 重构成更高效的动画函数
const animateElements = (isEntering, bgNextWidth, initialWidth) => {
    const gap = parseFloat(window.getComputedStyle(DOM.navSearchWrapper).gap) || 0;
    const totalOffset = isEntering ? bgNextWidth + gap : bgNextWidth;

    requestAnimationFrame(() => {
        setTransitions();
        
        const elements = [
            [DOM.bgNext, {
                opacity: isEntering ? "1" : "0",
                transform: `translateX(${isEntering ? "0" : "20px"})`
            }],
            [DOM.navSearchWrapper, {
                width: `${initialWidth + (isEntering ? bgNextWidth : -bgNextWidth)}px`
            }]
        ];

        if (DOM.searchbox) {
            elements.push([DOM.searchbox, {
                transform: `translateX(${isEntering ? "0" : `${bgNextWidth}px`})`
            }]);
        }

        if (DOM.divider) {
            elements.push([DOM.divider, {
                opacity: isEntering || DOM.searchbox ? "1" : "0",
                transform: `translateX(${isEntering ? "0" : `${bgNextWidth}px`})`
            }]);
        }

        elements.forEach(([el, styles]) => applyStyles(el, styles));
    });
};

// 页面过渡处理优化
const handlePageTransition = (isHomePage, state) => {
    if (isHomePage === state.lastPageWasHome) return;

    const bgNextWidth = measureElementWidth(DOM.bgNext);
    const initialWidth = DOM.navSearchWrapper.offsetWidth;

    animateTransition(isHomePage, state, bgNextWidth, initialWidth);
    state.lastPageWasHome = isHomePage;
    StateManager.setState(state);
};

// 执行过渡动画优化
const animateTransition = (isEntering, state, bgNextWidth, initialWidth) => {
    if (state.isTransitioning) return;
    StateManager.update({ isTransitioning: true });

    initElementStates(isEntering, bgNextWidth, initialWidth);
    
    [DOM.bgNext, DOM.navSearchWrapper, DOM.searchbox, DOM.divider]
        .filter(el => el)
        .forEach(el => el.offsetWidth);

    requestAnimationFrame(() => {
        setTransitions();
        animateElements(isEntering, bgNextWidth, initialWidth);
        
        setTimeout(() => {
            if (!isEntering) {
                applyStyles(DOM.bgNext, { display: 'none' });
                applyStyles(DOM.navSearchWrapper, { width: 'auto' });
                
                if (!DOM.searchbox && DOM.divider) {
                    applyStyles(DOM.divider, { display: 'none' });
                }
                
                [DOM.searchbox, DOM.divider]
                    .filter(el => el)
                    .forEach(el => {
                        el.style.transition = 'none';
                        el.style.transform = '';
                    });
            }
            StateManager.update({ isTransitioning: false });
        }, ANIMATION.durationMs);
    });
};

// 显示或隐藏bgNext元素优化
const showBgNext = () => {
    const isHomePage = ["/", "/index.php"].includes(location.pathname);
    const state = StateManager.getState();

    if (state.isTransitioning) return;

    if (state.firstLoad) {
        if (!state.initialized) {
            state.initialized = true;
            StateManager.setState(state);
            
            if (isHomePage) {
                setTimeout(() => {
                    const clone = DOM.bgNext.cloneNode(true);
                    clone.style.cssText = "display:block;opacity:0;position:fixed;pointer-events:none;";
                    document.body.appendChild(clone);
                    
                    const bgNextWidth = clone.offsetWidth;
                    document.body.removeChild(clone);
                    
                    const initialWidth = DOM.navSearchWrapper.offsetWidth;
                    
                    applyStyles(DOM.bgNext, {
                        display: 'block',
                        opacity: '0',
                        transform: 'translateX(20px)',
                        transition: 'none'
                    });
                    
                    if (DOM.searchbox) {
                        applyStyles(DOM.searchbox, {
                            transform: `translateX(${bgNextWidth}px)`,
                            transition: 'none'
                        });
                    }
                    
                    if (DOM.divider) {
                        applyStyles(DOM.divider, {
                            transform: `translateX(${bgNextWidth}px)`,
                            transition: 'none',
                            ...(DOM.searchbox ? {} : { opacity: '0' })
                        });
                    }
                    
                    [DOM.bgNext, DOM.navSearchWrapper, DOM.searchbox, DOM.divider]
                        .filter(el => el)
                        .forEach(el => el.offsetWidth);
                    
                    requestAnimationFrame(() => {
                        state.firstLoad = false;
                        StateManager.setState(state);
                        animateElements(true, bgNextWidth, initialWidth);
                    });
                }, 100);
                return;
            }
        }
        state.firstLoad = false;
        StateManager.setState(state);
        
        if (!isHomePage) {
            applyStyles(DOM.bgNext, { display: 'none' });
            if (!DOM.searchbox && DOM.divider) {
                applyStyles(DOM.divider, { display: 'none' });
            }
        }
        return;
    }
    handlePageTransition(isHomePage, state);
};

// 初始化文章标题行为优化
const initArticleTitleBehavior = () => {
    applyStyles(DOM.navSearchWrapper, { overflow: 'unset' });
    
    if (window._searchWrapperState) {
        const navTitle = DOM.navSearchWrapper.querySelector(".nav-article-title");
        if (navTitle) navTitle.remove();
        delete DOM.navSearchWrapper.dataset.scrollswap;
        DOM.navSearchWrapper.style.setProperty('--dw', '0');
        window._searchWrapperState = null;
    }
    
    if (!_iro.land_at_home) {
        const searchWrapperState = {
            state: false,
            navElement: null,
            navTitle: null,
            entryTitle: null,
            titlePadding: 20,
            scrollTimeout: null,
            hideTimeout: null,
            headerElement: null,
            
            init() {
                this.navTitle = DOM.navSearchWrapper.querySelector(".nav-article-title");
                this.entryTitle = document.querySelector(".entry-title");
                this.navElement = DOM.navSearchWrapper.querySelector("nav");
                this.header = document.querySelector("header");
                
                if (!this.navTitle) {
                    this.navTitle = document.createElement('div');
                    this.navTitle.classList.add('nav-article-title');
                    this.navTitle.style.opacity = '0';
                    DOM.navSearchWrapper.firstElementChild.insertAdjacentElement('afterend', this.navTitle);
                    
                    this.header.addEventListener('mouseenter', () => {
                        if (this.hideTimeout) {
                            clearTimeout(this.hideTimeout);
                            this.hideTimeout = null;
                        }
                        if (this.entryTitle && this.entryTitle.getBoundingClientRect().top < 0) {
                            this.hide();
                        }
                    });
                    
                    this.header.addEventListener('mouseleave', () => {
                        if (this.hideTimeout) clearTimeout(this.hideTimeout);
                        if (this.entryTitle && this.entryTitle.getBoundingClientRect().top < 0) {
                            this.hideTimeout = setTimeout(() => {
                                this.show();
                                this.hideTimeout = null;
                            }, 3000);
                        }
                    });
                    
                    this.navElement.addEventListener('transitionend', (event) => {
                        if (![this.navElement, this.header].includes(event.target)) return;
                        this.navTitle.style.opacity = window.getComputedStyle(this.navElement).transform === 'none' ? '0' : '1';
                        if (document.querySelector(".entry-title")) {
                            DOM.navSearchWrapper.style.overflow = window.getComputedStyle(this.navElement).transform === 'none' ? 'unset' : 'hidden';
                        }
                    });
                    
                    this.navElement.addEventListener('transitionstart', (event) => {
                        if (![this.navElement, this.header].includes(event.target)) return;
                        if (document.querySelector(".entry-title")) {
                            DOM.navSearchWrapper.style.overflow = 'hidden';
                        }
                        this.navTitle.style.opacity = '1';
                    });
                }
                this.updateTitle();
            },
            
            updateTitle() {
                if (this.entryTitle) {
                    this.navTitle.textContent = this.entryTitle.textContent;
                    this.navTitle.style.display = 'block';
                } else {
                    this.navTitle.style.display = 'none';
                }
            },
            
            show() {
                if (this.state || !this.entryTitle) return;
                DOM.navSearchWrapper.dataset.scrollswap = 'true';
                
                requestAnimationFrame(() => {
                    const tempNav = document.createElement('div');
                    tempNav.style.cssText = `
                        position: absolute;
                        visibility: hidden;
                        white-space: nowrap;
                        display: flex;
                        align-items: center;
                        padding: ${window.getComputedStyle(this.navElement).padding};
                        margin: ${window.getComputedStyle(this.navElement).margin};
                        gap: ${window.getComputedStyle(this.navElement).gap};
                    `;
                    document.body.appendChild(tempNav);
                    
                    Array.from(this.navElement.children).forEach(item => {
                        const clone = item.cloneNode(true);
                        const computedStyle = window.getComputedStyle(item);
                        clone.style.cssText = Array.from(computedStyle).reduce(
                            (str, prop) => `${str}${prop}:${computedStyle.getPropertyValue(prop)};`, 
                            ''
                        );
                        tempNav.appendChild(clone);
                    });
                    
                    const actualNavWidth = Math.ceil(tempNav.getBoundingClientRect().width);
                    const tempTitle = document.createElement('div');
                    const titleStyle = window.getComputedStyle(this.navTitle);
                    tempTitle.style.cssText = `
                        position: absolute;
                        visibility: hidden;
                        white-space: nowrap;
                        font-size: ${titleStyle.fontSize};
                        font-family: ${titleStyle.fontFamily};
                        font-weight: ${titleStyle.fontWeight};
                        letter-spacing: ${titleStyle.letterSpacing};
                        padding: ${titleStyle.padding};
                        margin: ${titleStyle.margin};
                    `;
                    tempTitle.textContent = this.navTitle.textContent;
                    document.body.appendChild(tempTitle);
                    const actualTitleWidth = Math.ceil(tempTitle.getBoundingClientRect().width);
                    const deltaWidth = actualTitleWidth - actualNavWidth;
                    DOM.navSearchWrapper.style.setProperty('--dw', `${deltaWidth}px`);
                    
                    document.body.removeChild(tempNav);
                    document.body.removeChild(tempTitle);
                });
                
                this.state = true;
            },
            
            hide() {
                if (!this.state) return;
                delete DOM.navSearchWrapper.dataset.scrollswap;
                DOM.navSearchWrapper.style.setProperty('--dw', '0');
                if (document.querySelector(".entry-title")) {
                    DOM.navSearchWrapper.style.overflow = 'unset';
                }
                this.state = false;
            },
            
            handleScroll() {
                if (this.scrollTimeout) clearTimeout(this.scrollTimeout);
                this.scrollTimeout = setTimeout(() => {
                    if (this.entryTitle && this.entryTitle.getBoundingClientRect().top < 0) {
                        this.show();
                    } else {
                        this.hide();
                    }
                }, 20);
            },
        };
        
        searchWrapperState.init();
        window.addEventListener('scroll', () => searchWrapperState.handleScroll(), { passive: true });
        window.addEventListener('resize', () => searchWrapperState.show(), { passive: true });
        searchWrapperState.handleScroll();
        window._searchWrapperState = searchWrapperState;
    } else {
        requestAnimationFrame(() => {
            DOM.navSearchWrapper.style.overflow = 'unset';
        });
    }
};

// 初始化所有动画
const initAnimations = () => {
    StateManager.init();
    showBgNext();
    initArticleTitleBehavior();
};

// 优化后的事件监听器
const addEventListeners = () => {
    const events = [
        { type: 'pjax:send', handler: () => 
            StateManager.update({
                lastPageWasHome: ["/", "/index.php"].includes(location.pathname)
            })
        },
        { type: 'pjax:complete', handler: () => 
            requestAnimationFrame(() => {
                showBgNext();
                window._searchWrapperState?.handleScroll();
            })
        },
        { type: 'DOMContentLoaded', handler: initAnimations }
    ];

    events.forEach(({ type, handler }) => 
        document.addEventListener(type, handler, { passive: true })
    );
};

// 初始化事件监听
addEventListeners();