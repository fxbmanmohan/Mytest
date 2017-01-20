<?php
session_start();
require_once'includes/functions.php';
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {// last request was more than 30 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
    ?>
    <script language="javascript" type="text/javascript">
        window.location.href = "<?= HOME_SCREEN ?>";
    </script>
    <?php
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

$obj = new DB_Class();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <!--Not Stored Cache-->
        <meta http-equiv="cache-control" content="max-age=0" />
        <meta http-equiv="cache-control" content="no-cache" />
        <meta http-equiv="expires" content="0" />
        <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
        <meta http-equiv="pragma" content="no-cache" />
        <!--Not Stored Cache-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?= SITE_NAME ?></title>
        <link rel="icon" href="images/ww_favicon.gif" type="image/gif" />
        <!-- JS Section Start Here -->
        <script type="text/javascript" src="js/location_tree_jquery.min.js"></script>
        <script type="text/javascript" src="js/jquery.alerts.js"></script>
        <script type="text/javascript" src="js/modal.popup.js"></script>
        <!-- JS Section Start Here -->
        <!-- CSS Section Start Here -->
        <link rel="stylesheet" href="jquery.alerts.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="style.css" type="text/css" />
        <link rel="stylesheet" href="menu_style.css" type="text/css" />
        <!--[if IE]><script language="javascript" type="text/javascript" src="../dist/excanvas.js"></script><![endif]-->
        <!--[if IE]><link href="style_ie.css" rel="stylesheet" type="text/css" /><![endif]-->
        <!-- CSS Section Start Here -->
        <style>
            #spinner{ display: none; width:99%; height: 100%; position: fixed; top: 0; left: 0; background:url(images/loadingAnimation.gif) no-repeat center #CCCCCC; filter:alpha(opacity=80) !important; text-align:center; padding:10px; font:normal 16px Tahoma, Geneva, sans-serif; border:1px solid #666; z-index:1000000000000 !important; overflow: auto; opacity : 0.8; }
        </style>

    </head>
    <?php $notAccepted = array('company', 'builder', 'tenant'); ?>
    <body <?php if (!in_array($_GET['sect'], $notAccepted)) { ?>onfocus="checkSessionFocus();" <?php } ?>>
        <div style="position:relative;">
            <div id="spinner" style="filter:alpha(opacity=40);"></div>  
            <?php
            if (isset($_SESSION['ww_is_company']) && $_SESSION['ww_is_company'] == 1) { // company
                include'includes/c_header.php';
            } elseif (isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 1) { // builder
                include'includes/b_header.php';
            } elseif (isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0) { // inspector
                include'includes/o_header.php';
            } elseif (isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 2) { // trade
                include'includes/r_header.php';
            } else { // default
                include'includes/header.php';
            }
            ?>
            <div id="middle">
                <?php if (isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0) { // inspector?>
                    <div id="leftNavigation">
                        <?php include'includes/o_navigation.php'; ?>
                    </div>
                <?php } ?>
                <div class="content_container" <?php
                if (isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0) {
                    echo 'style="width:755px;float:left;background-position:left; background-image:url(images/v-divider.png); background-repeat:repeat-y; padding-left:10px;margin-top:0px; padding-top:20px; min-height:725px;"';
                }
                ?>>
                    <div id="loggedMessage"></div>
                    <?php
                    if (isset($_GET['sect']))
                        $page = $_GET['sect'];
                    else
                        $page = 'index';

                    switch ($page) {

                        // General
                        case'forgot_password':
                            include'includes/forgot_password.php';
                            break;

                        case'log':
                            include'includes/log.php';
                            break;
                        // ------------------------------------------------------
                        // Company
                        case'company':
                            include'includes/company.php';
                            break;

                        case'c_show_project_detail':
                            include'includes/c_show_project_detail.php';
                            break;

                        case'c_full_analysis':
                            include'includes/c_full_analysis.php';
                            break;

                        case'c_dashboard':
                            include'includes/c_dashboard.php';
                            break;

                        case'c_dashboard_edit':
                            include'includes/c_dashboard_edit.php';
                            break;

                        case'c_builder':
                            include'includes/c_builder.php';
                            break;

                        case'c_add_builder':
                            include'includes/c_add_builder.php';
                            break;

                        case'c_remove_builder':
                            include'includes/c_remove_builder.php';
                            break;

                        case'c_changepassword':
                            include'includes/c_changepassword.php';
                            break;

                        case'c_report':
                            include'includes/c_report.php';
                            break;

                        case'c_defect':
                            include'includes/c_defect.php';
                            break;
                        // ------------------------------------------------------
                        // Builder
                        case'b_full_analysis':
                            include'includes/b_full_analysis.php';
                            break;

                        case'b_show_project_detail':
                            include'includes/b_show_project_detail.php';
                            break;

                        case'builder':
                            include'includes/builder.php';
                            break;

                        case'b_dashboard':
                            include'includes/b_dashboard.php';
                            break;

                        case'b_dashboard_edit':
                            include'includes/b_dashboard_edit.php';
                            break;

                        case'b_project':
                            include'includes/b_project.php';
                            break;

                        case'show_project':
                            include'includes/show_project.php';
                            break;

                        case'add_project':
                            include'includes/add_project.php';
                            break;

                        case'edit_project':
                            include'includes/edit_project.php';
                            break;

                        case'add_project_detail':
                            include'includes/add_project_detail.php';
                            break;

                        case'show_defects_list':
                            include'includes/show_defects_list.php';
                            break;

                        case'add_defects_list':
                            include'includes/add_defects_list.php';
                            break;

                        case'edit_defects_list':
                            include'includes/edit_defects_list.php';
                            break;

                        case'show_sub_loc':
                            include'includes/show_sub_loc.php';
                            break;

                        case'add_sub_loc':
                            include'includes/add_sub_loc.php';
                            break;

                        case'edit_sub_loc':
                            include'includes/edit_sub_loc.php';
                            break;

                        case'assign_to':
                            include'includes/assign_to.php';
                            break;

                        case'add_assign_to':
                            include'includes/add_assign_to.php';
                            break;

                        case'edit_assign_to':
                            include'includes/edit_assign_to.php';
                            break;

                        case'show_responsible':
                            include'includes/show_responsible.php';
                            break;

                        case'add_responsible':
                            include'includes/add_responsible.php';
                            break;

                        case'edit_responsible':
                            include'includes/edit_responsible.php';
                            break;

                        case'b_defect':
                            include'includes/b_defect.php';
                            break;

                        case'issue_photo':
                            include'includes/issue_photo.php';
                            break;

                        case'show_defect_photo':
                            include'includes/show_defect_photo.php';
                            break;

                        case'edit_defect':
                            include'includes/edit_defect.php';
                            break;

                        case'tenant':
                            include'includes/tenant.php';
                            break;

                        case'load_report':
                            include'load_report.php';
                            break;
                        case'project_configuration':
                            include'includes/project_configuration.php';
                            break;
                        /* Start construction_calendar */

                        case'construction_calendar':
                            include'includes/construction_calendar.php';
                            break;
                        /* End construction_calendar */

                        case'show_inspection':
                            include'includes/show_inspection.php';
                            break;
                        case'progress_monitoring':
                            include'includes/progress_monitoring.php';
                            break;
                        case'issue_to':
                            include'includes/issue_to.php';
                            break;

                        case'c_issue_to':
                            include'includes/c_issue_to.php';
                            break;
                        case'b_progress':
                            include'includes/b_progress.php';
                            break;

                        case'standard_defect':
                            include'includes/standard_defect.php';
                            break;

                        case'add_progress_task':
                            include'includes/add_progress_task.php';
                            break;

                        case'edit_progress_task':
                            include'includes/edit_progress_task.php';
                            break;

                        case'add_issue_to':
                            include'includes/add_issue_to.php';
                            break;

                        case'edit_issue_to':
                            include'includes/edit_issue_to.php';
                            break;

                        case'edit_standard_defect':
                            include'includes/edit_standard_defect.php';
                            break;

                        case'add_standard_defect':
                            include'includes/add_standard_defect.php';
                            break;

                        // ------------------------------------------------------
                        // Tenant / Inspactor / Owner
                        case'o_edit_defect':
                            include'includes/o_edit_defect.php';
                            break;

                        case'o_dashboard':
                            include'includes/o_dashboard.php';
                            break;

                        case'access_denied':
                            include'includes/access_denied.php';
                            break;

                        case'add_defect':
                            include'includes/add_defect.php';
                            break;

                        case'o_defect':
                            include'includes/o_defect.php';
                            break;

                        case'i_defect':
                            include'includes/i_defect.php';
                            break;

                        case'i_report'://Report 
                            include'includes/i_report.php';
                            break;

                        case'i_progress_monitor'://Progress Monitoring
                            include'includes/i_progress.php';
                            break;
                        // ------------------------------------------------------
                        // Repairer / Assign To / Responsible
                        case'responsible':
                            include'includes/responsible.php';
                            break;

                        case'r_dashboard':
                            include'includes/r_dashboard.php';
                            break;

                        case'r_defect':
                            include'includes/r_defect.php';
                            break;

                        case'r_edit_defect':
                            include'includes/r_edit_defect.php';
                            break;
                        //GS
                        case'permissions':
                            include'includes/permissions_setting.php';
                            break;

                        case'checklist':
                            include'includes/checklist.php';
                            break;

                        case'edit_checklist':
                            include'includes/edit_checklist.php';
                            break;

                        case'inspection_checklist':
                            include'includes/inspection_check.php';
                            break;

                        //GS
                        case'import_inspections':
                            include'includes/import_inspections.php';
                            break;

                        case'drawing_management':
                            include'includes/drawing_management.php';
                            break;

                        case'drawing_mgmt':
                            include'includes/drawing_management_new.php';
                            break;

                        case'edit_drawing_management':
                            include'includes/edit_drawing_image.php';
                            break;

                        case'sync_permission':
                            include'includes/sync_permission.php';
                            break;

                        case'project_manual':
                            include'includes/manual_chapter.php';
                            break;

                        case'edit_project_manual':
                            include'includes/edit_manual_chapter.php';
                            break;

                        case'manual_sub_chapter':
                            include'includes/manual_sub_folder.php';
                            break;

                        case'edit_manual_sub_chapter':
                            include'includes/edit_manual_sub_folder.php';
                            break;

                        case'manual_mangage_files':
                            include'includes/manual_mangage_files.php';
                            break;

                        case'edit_manual_mangage_files':
                            include'includes/edit_manual_mangage_files.php';
                            break;


                        case'search_chapter':
                            include'chapter_search.php';
                            break;

                        case'manual_file_view':
                            include'includes/manual_file_view.php';
                            break;

                        case'qa_task_monitoring':
                            include'includes/qa_task_monitoring.php';
                            break;

                        case'add_qa_task':
                            include'includes/add_qa_task.php';
                            break;

                        case'edit_qa_task':
                            include'includes/edit_qa_task.php';
                            break;

                        case'qa_task_search':
                            include'includes/qa_task_search.php';
                            break;

                        case'loc_level_sync_permission':
                            include'includes/location_level_sync_permission.php';
                            break;
                        case'dashboard_detail':
                            include'dashboard_detail.php';
                            break; // ------------------------------------------------------		
                        // ------------------------------------------------------		
                        // Default
                        default:
                            include'includes/404-error.php';
                            break;
                        // ------------------------------------------------------
                    }
                    ?>
                </div>
            </div>
            <?php include'includes/footer.php'; ?>
        </div>
    </body>
</html>