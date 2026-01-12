<div class="header-wrapper row">
    <div class="logo-wrapper">
        <a href="dashboard">
            <img class="img-fluid" src="images/<?php echo SYSTEM_LOGO_SM ?>"alt="">
        </a>
    </div>
    <div class="nav-right col-auto pull-right right-header p-0 ms-auto">
        <ul class="nav-menus">
            <li class="onhover-dropdown">
                <div class="notification-box">
                    <svg>
                        <use href="images/svg/icon-sprite.svg#Bell"></use>
                    </svg>
                    <?php echo $notifyCount > 0 || $user['created'] >= $weekAgoDateFormatted ? "<span class='rounded-pill badge-primary'> </span>" : ""; ?>
                </div>
                <div class="onhover-show-div notification-dropdown">
                    <h6 class="f-18 mb-0 dropdown-title">Notitications</h6>
                    <ul>
                        <?php
                            if($user['created'] >= $weekAgoDateFormatted){
                                $conn = $pdo->open();

                                try {
                                    // Update the SQL query to select notifications that are active and created within the last week
                                    $notifyStmt = $conn->prepare("SELECT * FROM `notifications` WHERE `id` = 1");
                                    $notifyStmt->execute();
                                    $notification = $notifyStmt->fetch();
                                    $titleText = substr($notification['title'], 0, 35);
                                    $bodyText = substr($notification['body'], 0, 90);
                                    echo "
                                        <li class='b-l-success border-4 mt-3'>
                                            <a href='notification?id=".$notification['id'],"'>
                                                <p class='mb-0'><small>" . date('M d, Y', strtotime($notification['created'])) . "</small></p>
                                                <h5><strong>".html_entity_decode($titleText)."...</strong></h5>
                                            </a>
                                        </li>";
                                    }
                                catch (PDOException $e) {
                                    echo $e->getMessage();
                                }
                                $pdo->close();
                                }
                        ?>
                        <?php
                            $conn = $pdo->open();

                            try {
                                // Update the SQL query to select notifications that are active and created within the last week
                                $notifyStmt = $conn->prepare("SELECT * FROM `notifications` WHERE `active` = TRUE AND `created` >= :weekAgoDate");
                                $notifyStmt->bindParam(':weekAgoDate', $weekAgoDateFormatted);
                                $notifyStmt->execute();
                                $notification = $notifyStmt->fetchAll();
                                foreach ($notification as $row) {
                                    $titleText = substr($row['title'], 0, 35);
                                    echo "
                                        <li class='b-l-success border-4 mt-3'>
                                            <a href='notification?id=" . $row['id'], "'>
                                                <p class='mb-0'><small>" . date('M d, Y', strtotime($row['created'])) . "</small></p>
                                                <h4><strong>" . html_entity_decode($titleText) . "...</strong></h4>
                                            </a>
                                        </li>";
                                }
                            }
                            catch (PDOException $e) {
                                echo $e->getMessage();
                            }
                            $pdo->close();
                        ?>
                        <li><a class="f-w-700" href="notifications">CHECK ALL</a></li>
                    </ul>
                </div>
            </li>
            <li class="profile-nav onhover-dropdown pe-0 py-0">
                <div class="d-flex align-items-center profile-media">
                    <img class="b-r-25" src="images/dashboard/profile.png" alt="">
                    <div class="flex-grow-1 user"><span><?php echo $user['fname'] . ' ' . $user['lname']; ?></span>
                        <p class="mb-0 font-nunito"><?php echo $levelDisplay ?>
                            <i class="fa fa-chevron-down"></i>
                        </p>
                    </div>
                </div>
                <ul class="profile-dropdown onhover-show-div">
                    <li><a href="profile"><i class="fa fa-user"></i><span>Account </span></a></li>
                    <li><a href="notifications"><i class="fa fa-bell"></i><span>Notifications</span></a></li>
                    <li><a href="taskboard"><i class="fa fa-file-text"></i><span>Taskboard</span></a></li>
                    <li><a href="settings"><i class="fa fa-cog"></i><span>Settings</span></a>
                    </li>
                    <li><a href="logout"><i class="fa fa-right-from-bracket"> </i><span>Log Out</span></a></li>
                </ul>
            </li>
        </ul>
    </div>
</div>