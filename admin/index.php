<?php
session_start();
if (!isset($_SESSION["valid"])) {
  header("Location: login.php");  //redelixt to index.php (បើមិនបាន​ login រួចហើយ ទេ​នោះ​ទៅ​ទំព័រ​ login)
  exit(0);
}

?>

<!doctype html>
<html lang="en">

<?php include 'components/head.php';
?>
<title>Cozastore</title>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <!-- Sidebar scroll-->
      <?php
      if (isset($_SESSION['message'])) {
        ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <strong>Hey!</strong> <?= $_SESSION['message'] ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
        unset($_SESSION['message']);
      }
      ?>
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="./index.php" class="text-nowrap logo-img">
            <h2> Cozastore</h2>
          </a>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
          </div>
        </div>

        <!-- Sidebar navigation-->
        <?php include 'components/sidebarNavigation.php'; ?>

        <!-- End Sidebar navigation -->
      </div>
      <!-- End Sidebar scroll-->
    </aside>
    <!--  Sidebar End -->
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <!--  Header Start app-header -->
      <?php include 'components/appHeader.php'; ?>

      <!--  Header End -->
      <div class="container-fluid">
        <!--  Row 1 -->
        <?php include 'components/row1.php'; ?>

        <!--  Row 2 -->
        <?php include 'components/row2.php'; ?>

        <!--  Row 3 -->
        <?php include 'components/row3.php'; ?>


      </div>
    </div>
  </div>
  <?php include 'components/js.php' ?>
</body>

<html>