<header class="dashboard-header">
    <div>
        <h1><?php echo $pageTitle ?? 'Overview'; ?></h1>
        <div class="date-badge">
            <i class="fa-regular fa-calendar"></i>
            <?php echo date('l, F j, Y'); ?>
        </div>
    </div>
    <div class="user-profile">
         <div class="user-info" style="text-align: right;">
             <div class="name"><?php echo $_SESSION['full_name'] ?? 'Focal Person'; ?></div>
             <div class="role">Linkage Focal Person</div>
         </div>
         <div class="brand-icon" style="width:40px; height:40px; font-size:1rem;">
             <i class="fa-solid fa-user"></i>
         </div>
    </div>
</header>
