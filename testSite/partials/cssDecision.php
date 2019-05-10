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
        input.btn-custom, button.btn-custom{
            background-color: #70382D;
            color: #ffffff;
            border-color: #70382D;
        }

        a.btn-custom:hover,
        label.btn-custom:hover,
        input.btn-custom:hover,
        button.btn-custom:hover,
        .btn-custom.active,
        .btn-custom.active:hover {
            background-color: #49241c;
            color: #ffffff;
        }

        #jumbotron a, #table a{
            color: #70382D;
            text-decoration: none;
        }

        #jumbotron a:hover, #table a:hover {
            color: #49241c;
            text-decoration: none;
            background-color: inherit;
        }

        .previous {
            background-color: #f1f1f1;
            color: black;
            text-decoration: none;
        }

        .previous:hover {
            text-decoration: none;
        }

        .next {
            background-color: #70382D;
            color: white;
            text-decoration: none;
        }

        .next:hover {
            background-color: #49241c;
            color: white;
            text-decoration: none;
        }

        .round {
            border-radius: 50%;
        }

        th{
            color: #70382D;
        }

        a figcaption{
            color: #70382D;
            text-decoration: none;
        }

        .imageDiv a:hover {
            text-decoration: none;
        }

        .panel .panel-heading a h4{
            background-color:#70382D;
            color: #FFFFFF;
            text-decoration: none;
            border-radius: 3px;
            padding:6px;
        }

        .panel .panel-heading a:hover, .panel .panel-heading a h4:hover {
            background-color:#49241c;
            text-decoration: none;
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
        input.btn-custom, button.btn-custom{
            background-color: #3c8a2e;
            color: #ffffff;
            border-color: #3c8a2e;
        }

        a.btn-custom:hover,
        label.btn-custom:hover,
        input.btn-custom:hover,
        button.btn-custom:hover,
        .btn-custom.active,
        .btn-custom.active:hover {
            background-color: #265e1c;
            color: #ffffff;
        }

        #jumbotron a, #table a{
            color: #3c8a2e;
            text-decoration: none;
        }

        #jumbotron a:hover, #table a:hover {
            color: #265e1c;
            text-decoration: none;
            background-color: inherit;
        }

        .previous {
            background-color: #f1f1f1;
            color: black;
            text-decoration: none;
        }

        .previous:hover {
            text-decoration: none;
        }

        .next {
            background-color: #3c8a2e;
            color: white;
            text-decoration: none;
        }

        .next:hover {
            background-color: #265e1c;
            color: white;
            text-decoration: none;
        }

        .round {
            border-radius: 50%;
        }

        th{
            color: #3c8a2e;
        }

        a figcaption{
            color: #3c8a2e;
            text-decoration: none;
        }

        .imageDiv a:hover {
            text-decoration: none;
        }

        .panel .panel-heading a h4{
            background-color:#3c8a2e;
            color: #FFFFFF;
            text-decoration: none;
            border-radius: 3px;
            padding:6px;
        }

        .panel .panel-heading a:hover, .panel .panel-heading a h4:hover {
            background-color:#265e1c;
            text-decoration: none;
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
        input.btn-custom, button.btn-custom{
            background-color: #ffb652;
            color: #ffffff;
            border-color: #ffb652;
        }

        a.btn-custom:hover,
        label.btn-custom:hover,
        input.btn-custom:hover,
        button.btn-custom:hover,
        .btn-custom.active,
        .btn-custom.active:hover {
            background-color: #e8911b;
            color: #ffffff;
        }

        #jumbotron a, #table a{
            color: #ffb652;
            text-decoration: none;
        }

        #jumbotron a:hover, #table a:hover {
            color: #e8911b;
            text-decoration: none;
            background-color: inherit;
        }

        .previous {
            background-color: #f1f1f1;
            color: black;
            text-decoration: none;
        }

        .previous:hover {
            text-decoration: none;
        }

        .next {
            background-color: #ffb652;
            color: white;
            text-decoration: none;
        }

        .next:hover {
            background-color: #e8911b;
            color: white;
            text-decoration: none;
        }

        .round {
            border-radius: 50%;
        }

        th{
            color: #ffb652;
        }

        a figcaption{
            color: #ffb652;
            text-decoration: none;
        }

        .imageDiv a:hover {
            text-decoration: none;
        }

        .panel .panel-heading a h4{
            background-color:#ffb652;
            color: #FFFFFF;
            text-decoration: none;
            border-radius: 3px;
            padding:6px;
        }

        .panel .panel-heading a:hover, .panel .panel-heading a h4:hover {
            background-color:#e8911b;
            text-decoration: none;
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
        input.btn-custom, button.btn-custom{
            background-color: #165788;
            color: #ffffff;
            border-color: #165788;
        }

        a.btn-custom:hover,
        label.btn-custom:hover,
        input.btn-custom:hover,
        button.btn-custom:hover,
        .btn-custom.active,
        .btn-custom.active:hover {
            background-color: #114770;
            color: #ffffff;
        }

        #jumbotron a, #table a{
            color: #165788;
            text-decoration: none;
        }

        #jumbotron a:hover, #table a:hover {
            color: #114770;
            text-decoration: none;
            background-color: inherit;
        }

        .previous {
            background-color: #f1f1f1;
            color: black;
            text-decoration: none;
        }

        .previous:hover {
            text-decoration: none;
        }

        .next {
            background-color: #165788;
            color: white;
            text-decoration: none;
        }

        .next:hover {
            background-color: #114770;
            color: white;
            text-decoration: none;
        }

        .round {
            border-radius: 50%;
        }

        th{
            color: #165788;
        }

        a figcaption{
            color: #165788;
            text-decoration: none;
        }

        .imageDiv a:hover {
            text-decoration: none;
        }

        .panel .panel-heading a h4{
            background-color:#165788;
            color: #FFFFFF;
            text-decoration: none;
            border-radius: 3px;
            padding:6px;
        }

        .panel .panel-heading a:hover, .panel .panel-heading a h4:hover {
            background-color:#114770;
            text-decoration: none;
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
        input.btn-custom, button.btn-custom{
            background-color: #824bb0;
            color: #ffffff;
            border-color: #824bb0;
        }

        a.btn-custom:hover,
        label.btn-custom:hover,
        input.btn-custom:hover,
        button.btn-custom:hover,
        .btn-custom.active,
        .btn-custom.active:hover {
            background-color: #633589;
            color: #ffffff;
        }

        #jumbotron a, #table a{
            color: #824bb0;
            text-decoration: none;
        }

        #jumbotron a:hover, #table a:hover {
            color: #633589;
            text-decoration: none;
            background-color: inherit;
        }

        .previous {
            background-color: #f1f1f1;
            color: black;
            text-decoration: none;
        }

        .previous:hover {
            text-decoration: none;
        }

        .next {
            background-color: #824bb0;
            color: white;
            text-decoration: none;
        }

        .next:hover {
            background-color: #633589;
            color: white;
            text-decoration: none;
        }

        .round {
            border-radius: 50%;
        }

        th{
            color: #633589;
        }

        a figcaption{
            color: #824bb0;
            text-decoration: none;
        }

        .imageDiv a:hover {
            text-decoration: none;
        }

        .panel .panel-heading a h4{
            background-color:#824bb0;
            color: #FFFFFF;
            text-decoration: none;
            border-radius: 3px;
            padding:6px;
        }

        .panel .panel-heading a:hover, .panel .panel-heading a h4:hover {
            background-color:#633589;
            text-decoration: none;
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
        input.btn-custom, button.btn-custom{
            background-color: #bd3632;
            color: #ffffff;
            border-color: #bd3632;
        }

        a.btn-custom:hover,
        label.btn-custom:hover,
        input.btn-custom:hover,
        button.btn-custom:hover,
        .btn-custom.active,
        .btn-custom.active:hover {
            background-color: #911d1a;
            color: #ffffff;
        }

        #jumbotron a, #table a{
            color: #bd3632;
            text-decoration: none;
        }

        #jumbotron a:hover, #table a:hover {
            color: #911d1a;
            text-decoration: none;
            background-color: inherit;
        }

        .previous {
            background-color: #f1f1f1;
            color: black;
            text-decoration: none;
        }

        .previous:hover {
            text-decoration: none;
        }

        .next {
            background-color: #bd3632;
            color: white;
            text-decoration: none;
        }

        .next:hover {
            background-color: #911d1a;
            color: white;
            text-decoration: none;
        }

        .round {
            border-radius: 50%;
        }

        th{
            color: #bd3632;
        }

        a figcaption{
            color: #bd3632;
            text-decoration: none;
        }

        .imageDiv a:hover {
            text-decoration: none;
        }

        .panel .panel-heading a h4{
            background-color:#bd3632;
            color: #FFFFFF;
            text-decoration: none;
            border-radius: 3px;
            padding:6px;
        }

        .panel .panel-heading a:hover, .panel .panel-heading a h4:hover {
            background-color:#911d1a;
            text-decoration: none;
        }

    </style>
<?php } ?>