<?php if ($_GET['Database'] == "avian" || $_GET['Database'] == "herpetology" || $_GET['Database'] == "mammal") {?>
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
<?php } else if($_GET['Database'] == "vwsp" || $_GET['Database'] == "algae" || $_GET['Database'] == "fungi" || $_GET['Database'] == "bryophytes" || $_GET['Database'] == "lichen") {?>
    <style>
        div h1 {
            background: #3c8a2e;
            color: #ffffff;
            margin-bottom: 0px;
            margin-right: 0px;
            margin-left: 0px;
            padding: 0px 15px;
        }

        input[type="radio"], input[type="button"], a[role="button"] {
            background: #3c8a2e;
            border-color: #3c8a2e;
        }

        label.btn-custom, a.btn-custom,
        input.btn-custom {
            background-color: #3c8a2e;
            color: #ffffff;
            border-color: #3c8a2e;
        }

        a.btn-custom:hover,
        label.btn-custom:hover,
        input.btn-custom:hover,
        .btn-custom.active,
        .btn-custom.active:hover {
            background-color: #265e1c;
            color: #ffffff;
        }

        #jumbotron a{
            color: #265e1c;
            text-decoration: none;
        }

        #jumbotron a:hover {
            color: #49241c;
        }
    </style>
<?php } else if ($_GET['Database'] == "miw" || $_GET['Database'] == "mi") {?>
    <style>
        div h1 {
            background: #ffb652;
            color: #ffffff;
            margin-bottom: 0px;
            margin-right: 0px;
            margin-left: 0px;
            padding: 0px 15px;
        }

        input[type="radio"], input[type="button"], a[role="button"] {
            background: #ffb652;
            border-color: #ffb652;
        }

        label.btn-custom, a.btn-custom,
        input.btn-custom {
            background-color: #ffb652;
            color: #ffffff;
            border-color: #ffb652;
        }

        a.btn-custom:hover,
        label.btn-custom:hover,
        input.btn-custom:hover,
        .btn-custom.active,
        .btn-custom.active:hover {
            background-color: #e8911b;
            color: #ffffff;
        }

        #jumbotron a{
            color: #ffb652;
            text-decoration: none;
        }

        #jumbotron a:hover {
            color: #e8911b;
        }
    </style>
<?php } else if ($_GET['Database'] == "fish") {?>
    <style>
        div h1 {
            background: #165788;
            color: #ffffff;
            margin-bottom: 0px;
            margin-right: 0px;
            margin-left: 0px;
            padding: 0px 15px;
        }

        input[type="radio"], input[type="button"], a[role="button"] {
            background: #165788;
            border-color: #165788;
        }

        label.btn-custom, a.btn-custom,
        input.btn-custom {
            background-color: #165788;
            color: #ffffff;
            border-color: #165788;
        }

        a.btn-custom:hover,
        label.btn-custom:hover,
        input.btn-custom:hover,
        .btn-custom.active,
        .btn-custom.active:hover {
            background-color: #114770;
            color: #ffffff;
        }

        #jumbotron a{
            color: #165788;
            text-decoration: none;
        }

        #jumbotron a:hover {
            color: #114770;
        }
    </style>
<?php } else if ($_GET['Database'] == "entomology") {?>
    <style>
        div h1 {
            background: #824bb0;
            color: #ffffff;
            margin-bottom: 0px;
            margin-right: 0px;
            margin-left: 0px;
            padding: 0px 15px;
        }

        input[type="radio"], input[type="button"], a[role="button"] {
            background: #824bb0;
            border-color: #824bb0;
        }

        label.btn-custom, a.btn-custom,
        input.btn-custom {
            background-color: #824bb0;
            color: #ffffff;
            border-color: #824bb0;
        }

        a.btn-custom:hover,
        label.btn-custom:hover,
        input.btn-custom:hover,
        .btn-custom.active,
        .btn-custom.active:hover {
            background-color: #633589;
            color: #ffffff;
        }

        #jumbotron a{
            color: #824bb0;
            text-decoration: none;
        }

        #jumbotron a:hover {
            color: #633589;
        }
    </style>
<?php } else if ($_GET['Database'] == "fossil") {?>
    <style>
        div h1 {
            background: #bd3632;
            color: #ffffff;
            margin-bottom: 0px;
            margin-right: 0px;
            margin-left: 0px;
            padding: 0px 15px;
        }

        input[type="radio"], input[type="button"], a[role="button"] {
            background: #bd3632;
            border-color: #bd3632;
        }

        label.btn-custom, a.btn-custom,
        input.btn-custom {
            background-color: #bd3632;
            color: #ffffff;
            border-color: #bd3632;
        }

        a.btn-custom:hover,
        label.btn-custom:hover,
        input.btn-custom:hover,
        .btn-custom.active,
        .btn-custom.active:hover {
            background-color: #911d1a;
            color: #ffffff;
        }

        #jumbotron a{
            color: #bd3632;
            text-decoration: none;
        }

        #jumbotron a:hover {
            color: #911d1a;
        }
    </style>
<?php } ?>