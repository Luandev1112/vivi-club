{{--
 * JobClass - Job Board Web Application
 * Copyright (c) BedigitCom. All Rights Reserved
 *
 * Website: https://bedigit.com
 *
 * LICENSE
 * -------
 * This software is furnished under a license and may be used and copied
 * only in accordance with the terms of such license and with the inclusion
 * of the above copyright notice. If you Purchased from CodeCanyon,
 * Please read the full License from here - http://codecanyon.net/licenses/standard
--}}
@extends('layouts.master')

@section('content')
	@includeFirst([config('larapen.core.customizedViewPath') . 'common.spacer', 'common.spacer'])
    <div class="main-container">
        <div class="container">
            <div class="row">
    
                @if (Session::has('flash_notification'))
                    <div class="col-xl-12">
                        <div class="row">
                            <div class="col-xl-12">
                                @include('flash::message')
                            </div>
                        </div>
                    </div>
                @endif
    
                <div class="col-md-3 page-sidebar">
                    @includeFirst([config('larapen.core.customizedViewPath') . 'account.inc.sidebar', 'account.inc.sidebar'])
                </div>
                <!--/.page-sidebar-->
                
                <div class="col-md-9 page-content">
                    <div class="inner-box">
                        <h2 class="title-2"><i class="fas fa-edit"></i> Compose Mail </h2>
                        <div class="inbox-wrapper">
                            <div class="row">
                                <div class="col-md-3 col-lg-2">
                                    <div class="btn-group hidden-sm">
                                        <a href="account-message-compose.html" class="btn btn-primary text-uppercase"
                                        ><i class="fas fa-plus"></i> Compose
                                        </a>
                                    </div>
                                </div>

                                <div class="col-md-9 col-lg-10 ">
                                    <div class="user-bar-top">
                                        <div class="user-top-comp">
                                            <div class="col m-0 p-0">
                                                <label class="sr-only" for="inlineFormInputGroup"> </label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">To:</div>
                                                    </div>
                                                    <input type="text" class="form-control" id="inlineFormInputGroup" placeholder="Type the name of a person or user..">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3 col-lg-2 hidden-sm">
                                    <ul class="nav nav-pills  inbox-nav">
                                        <li class="active nav-item"><a class="nav-link" href="account-message-inbox.html"> Inbox
                                            <span class="badge badge-gray count">32</span></a>
                                        </li>
                                        <li class="nav-item"><a class="nav-link">Drafts <span
                                                class="badge badge-gray count">6</span></a>
                                        </li>
                                        <li class="nav-item"><a class="nav-link">Starred</a>
                                        </li>
                                        <li class="nav-item"><a class="nav-link">Important</a>
                                        </li>
                                        <li class="nav-item"><a class="nav-link">Sent
                                            Mail</a>
                                        </li>

                                    </ul>
                                </div>

                                <div class="col-md-9 col-lg-10 chat-row">
                                    <div class="message-compose">

                                            <div class="type-form">
                                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" placeholder="Type a message...">Type a message...
                                                </textarea>
                                            </div>

                                            <div class="type-form-footer">
                                              <a href="#" class="btn btn-default"> <i class="fas fa-trash-alt"></i> </a>
                                                <a href="#" class="btn btn-default btn-icon">
                                                    Draft
                                                    <i class="fas fa-tag"></i> </a>
                                                <a class="btn btn-success btn-icon">
                                                    Send
                                                    <i class="fas fa-envelope"></i>
                                                </a>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--/. inbox-wrapper-->
                    </div>
                </div>
                <!--/.page-content-->
            </div>
            <!--/.row-->
        </div>
        <!--/.container-->
    </div>
    <!-- /.main-container -->
@endsection

@section('after_scripts')
	<?php
	/*
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="assets/js/jquery/jquery-3.3.1.min.js">\x3C/script>')</script>
	<script src="assets/bootstrap/js/bootstrap.bundle.js"></script>
	<script src="assets/js/vendors.min.js"></script>
	
	<!-- include custom script for site -->
	<script src="assets/js/main.min.js"></script>
	*/
	?>
	
	<!-- include custom script for ads table [select all checkbox] -->
	<script>
		function checkAll(bx) {
			var chkinput = document.getElementsByTagName('input');
			for (var i = 0; i < chkinput.length; i++) {
				if (chkinput[i].type == 'checkbox') {
					chkinput[i].checked = bx.checked;
				}
			}
		}
	</script>
@endsection