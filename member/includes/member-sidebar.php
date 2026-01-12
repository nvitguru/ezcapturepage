<div class="sidebar-wrapper" data-layout="stroke-svg">
    <div>
        <div class="logo-wrapper">
            <a href="dashboard">
                <img class="img-fluid" src="images/<?php echo SYSTEM_ICON ?>" alt="<?php echo SYSTEM_NAME ?>" style="max-width: 50px;"></a>
            <div class="back-btn"><i class="fa fa-angle-left"> </i></div>
        </div>
        <nav class="sidebar-main">
            <div id="sidebar-menu">
                <ul class="sidebar-links" id="simple-bar">
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="dashboard">
                            <svg class="stroke-icon">
                                <use href="images/svg/icon-sprite.svg#home"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="images/svg/icon-sprite.svg#fill-home"></use>
                            <span>Home</span>
                        </a>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                            <svg class="stroke-icon">
                                <use href="images/svg/icon-sprite.svg#stroke-widget"></use>

                            <svg class="fill-icon">
                                <use href="images/svg/icon-sprite.svg#fill-widget"></use>
                            </svg><span>Builder</span>
                        </a>
                        <ul class="sidebar-submenu custom-scrollbar">
                            <li class="main-submenu"><a class="d-flex sidebar-menu" href="javascript:void(0)">
                                <svg class="stroke-icon">
                                    <use href="images/svg/icon-sprite.svg#stroke-landing-page"></use>
                                </svg>
                                <svg class="fill-icon">
                                    <use href="images/svg/icon-sprite.svg#fill-landing-page"></use>
                                </svg>Pages
                                <svg class="arrow">
                                    <use href="images/svg/icon-sprite.svg#Arrow-right"></use>
                                </svg></a>
                                <ul class="submenu-wrapper">
                                    <li class="text-start">
                                        <form id="createPage" method="post" action="createPage">
                                            <button type="submit" name="createPage" style="background: none; border: none; padding: 0; cursor: pointer;">
                                                <a class="d-flex">
                                                    Create Page
                                                </a>
                                            </button>
                                        </form>
                                    </li>
                                    <li><a href="view-pages">View All Pages</a></li>
                                </ul>
                            </li>
                            <li class="main-submenu"><a class="d-flex sidebar-menu" href="javascript:void(0)">
                                    <svg class="stroke-icon">
                                        <use href="images/svg/icon-sprite.svg#stroke-form"></use>
                                    </svg>
                                    <svg class="fill-icon">
                                        <use href="images/svg/icon-sprite.svg#fill-form"></use>
                                    </svg>Forms
                                    <svg class="arrow">
                                        <use href="images/svg/icon-sprite.svg#Arrow-right"></use>
                                    </svg></a>
                                <ul class="submenu-wrapper">
                                    <li class="text-start">
                                        <form id="createForm" method="post" action="createForm">
                                            <button type="submit" name="createForm" style="background: none; border: none; padding: 0; cursor: pointer;">
                                                <a class="d-flex">
                                                    Create Form
                                                </a>
                                            </button>
                                        </form>
                                    </li>
                                    <li><a href="view-forms">View All Forms</a></li>
                                </ul>
                            </li>
                            <li class="main-submenu">
                                <a class="d-flex sidebar-menu" href="javascript:void(0)">
                                    <svg class="stroke-icon">
                                        <use href="images/svg/icon-sprite.svg#stroke-email"></use>
                                    </svg>
                                    <svg class="fill-icon">
                                        <use href="images/svg/icon-sprite.svg#fill-email"></use>
                                    </svg>CRMs
                                    <svg class="arrow">
                                        <use href="images/svg/icon-sprite.svg#Arrow-right"></use>
                                    </svg>
                                </a>
                                <ul class="submenu-wrapper">
                                    <li class="text-start">
                                        <form id="createCRM" method="post" action="createCRM">
                                            <button type="submit" name="createCRM" style="background: none; border: none; padding: 0; cursor: pointer;">
                                                <a class="d-flex">
                                                    Create CRM
                                                </a>
                                            </button>
                                        </form>
                                    </li>
                                    <li><a href="view-crms">View All CRMs</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                            <svg class="stroke-icon">
                                <use href="images/svg/icon-sprite.svg#Setting"></use>

                                <svg class="fill-icon">
                                    <use href="images/svg/icon-sprite.svg#fill-Setting"></use>
                                </svg><span>Settings</span>
                        </a>
                        <ul class="sidebar-submenu custom-scrollbar">
                            <li class="main-submenu">
                                <a class="d-flex sidebar-menu" href="profile">
                                    <svg class="stroke-icon">
                                        <use href="images/svg/icon-sprite.svg#stroke-user"></use>
                                    </svg>
                                    <svg class="fill-icon">
                                        <use href="images/svg/icon-sprite.svg#fill-user"></use>
                                    </svg>Profile
                                </a>
                            </li>
                            <li class="main-submenu">
                                <a class="d-flex sidebar-menu" href="account">
                                    <svg class="stroke-icon">
                                        <use href="images/svg/icon-sprite.svg#stroke-blog"></use>
                                    </svg>
                                    <svg class="fill-icon">
                                        <use href="images/svg/icon-sprite.svg#fill-blog"></use>
                                    </svg>Account
                                </a>
                            </li>
                            <li class="main-submenu">
                                <a class="d-flex sidebar-menu" href="password">
                                    <svg class="stroke-icon">
                                        <use href="images/svg/icon-sprite.svg#stroke-internationalization"></use>
                                    </svg>
                                    <svg class="fill-icon">
                                        <use href="images/svg/icon-sprite.svg#fill-internationalization"></use>
                                    </svg>Password
                                </a>
                            </li>
                            <li class="main-submenu">
                                <a class="d-flex sidebar-menu" href="affiliate">
                                    <svg class="stroke-icon">
                                        <use href="images/svg/icon-sprite.svg#stroke-learning"></use>
                                    </svg>
                                    <svg class="fill-icon">
                                        <use href="images/svg/icon-sprite.svg#fill-learning"></use>
                                    </svg>Affiliate
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="javascript:void(0)">
                            <svg class="stroke-icon">
                                <use href="images/svg/icon-sprite.svg#stroke-others"></use>

                                <svg class="fill-icon">
                                    <use href="images/svg/icon-sprite.svg#fill-others"></use>
                                </svg><span>Resource</span>
                        </a>
                        <ul class="sidebar-submenu custom-scrollbar">
                            <li class="main-submenu">
                                <a class="d-flex sidebar-menu" href="marketing">
                                    <svg class="stroke-icon">
                                        <use href="images/svg/icon-sprite.svg#stroke-ecommerce"></use>
                                    </svg>
                                    <svg class="fill-icon">
                                        <use href="images/svg/icon-sprite.svg#fill-ecommerce"></use>
                                    </svg>Marketing
                                </a>
                            </li>
                            <li class="main-submenu">
                                <a class="d-flex sidebar-menu" href="learning">
                                    <svg class="stroke-icon">
                                        <use href="images/svg/icon-sprite.svg#stroke-file"></use>
                                    </svg>
                                    <svg class="fill-icon">
                                        <use href="images/svg/icon-sprite.svg#fill-file"></use>
                                    </svg>Learning
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="notifications">
                            <svg class="stroke-icon">
                                <use href="images/svg/icon-sprite.svg#Bell"></use>
                            </svg>
                            <span>Alerts</span>
                        </a>
                    </li>
                    <li class="sidebar-list"><a class="sidebar-link sidebar-title" href="javascript:void(0)">
                        <i class="fa fa-question stroke-icon"></i>
                        <i class="fa fa-question fill-icon"></i>
                        <span>Support</span></a>
                        <ul class="sidebar-submenu custom-scrollbar">
                            <li class="main-submenu">
                                <a class="d-flex sidebar-menu" href="faqs">
                                    <svg class="stroke-icon">
                                        <use href="images/svg/icon-sprite.svg#stroke-faq"></use>
                                    </svg>
                                    <svg class="fill-icon">
                                        <use href="images/svg/icon-sprite.svg#fill-faq"></use>
                                    </svg>FAQ's
                                </a>
                            </li>
                            <li class="main-submenu">
                                <a class="d-flex sidebar-menu" href="support">
                                    <svg class="stroke-icon">
                                        <use href="images/svg/icon-sprite.svg#stroke-email"></use>
                                    </svg>
                                    <svg class="fill-icon">
                                        <use href="images/svg/icon-sprite.svg#fill-email"></use>
                                    </svg>Member Support
                                </a>
                            </li>
                            <li class="main-submenu">
                                <a class="d-flex sidebar-menu" href="legal">
                                    <svg class="stroke-icon">
                                        <use href="images/svg/icon-sprite.svg#stroke-knowledgebase"></use>
                                    </svg>
                                    <svg class="fill-icon">
                                        <use href="images/svg/icon-sprite.svg#fill-knowledgebase"></use>
                                    </svg>Legal
                                </a>
                            </li>
                            <li class="main-submenu">
                                <a class="d-flex sidebar-menu"  onclick="$crisp.push(['do', 'chat:open'])" href="#">
                                    <svg class="stroke-icon">
                                        <use href="images/svg/icon-sprite.svg#stroke-chat"></use>
                                    </svg>
                                    <svg class="fill-icon">
                                        <use href="images/svg/icon-sprite.svg#fill-chat"></use>
                                    </svg>Live Chat
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title" href="logout">
                            <i class="fa fa-sign-out stroke-icon"></i>
                            <i class="fa fa-sign-out fill-icon"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>