<?php if ($_GET['Database'] == "avian" || $_GET['Database'] == "herptology" || $_GET['Database'] == "mammal") {?>
  <style>
    div h1 {
      background: #70382D;
      color: #ffffff;
      margin-bottom: 0px;
      margin-right: 0px;
      margin-left: 0px;
      padding: 0px 15px;
    }

    input[type="radio"], input[type="button"], a[role="button"] {
      background: #70382D;
      border-color: #70382D;
    }

    label.btn-custom, a.btn-custom,
    input.btn-custom {
          background-color: #70382D;
          color: #ffffff;
          border-color: #70382D;
    }

    a.btn-custom:hover,
    label.btn-custom:hover,
    input.btn-custom:hover,
    .btn-custom.active,
    .btn-custom.active:hover {
      background-color: #49241c;
      color: #ffffff;
    }

    #jumbotron a{
      color: #70382D;
      text-decoration: none;
    }

    #jumbotron a:hover {
      color: #49241c;
    }
  </style>
  <?php }?>