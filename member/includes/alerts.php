<?php
if(isset($_SESSION['error'])){
    echo "
          <div class='row'>
              <div class='col-12 mt-2'>
                <div class='alert alert-fade alert-danger'>
                    <i class='fa fa-fa fa-exclamation-triangle'></i> ".$_SESSION['error']."
                </div>
              </div>
          </div>
        ";
    unset($_SESSION['error']);
}
if(isset($_SESSION['success'])){
    echo "
          <div class='row'>
              <div class='col-12 mt-2'>
                <div class='alert alert-fade alert-success'>
                    <i class='fa fa-thumbs-up'></i> ".$_SESSION['success']."
                </div>
              </div>
          </div>
        ";
    unset($_SESSION['success']);
}
if(isset($_SESSION['warning'])){
    echo "
          <div class='row'>
              <div class='col-12 mt-2'>
                <div class='alert alert-fade alert-warning'>
                  <i class='fa fa-exclamation-triangle'></i> ".$_SESSION['warning']."
                </div>
              </div>
          </div>
        ";
    unset($_SESSION['warning']);
}
if(isset($_SESSION['info'])){
    echo "
          <div class='row'>
              <div class='col-12 mt-2'>
                <div class='alert alert-fade alert-primary'>
                    <i class='fa fa-info-circle'></i> ".$_SESSION['info']."
                </div>
              </div>
          </div>
        ";
    unset($_SESSION['info']);
}