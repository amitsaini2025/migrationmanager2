           <!-- Appointment Tab -->
           <div class="tab-pane" id="appointments-tab">
                <div class="card-header-action text-right" style="padding-bottom:15px;">
                    <a href="javascript:;" data-toggle="modal" data-target="#create_appoint" class="btn btn-primary createaddapointment"><i class="fa fa-plus"></i> Add Appointment</a>
                </div>
                <div class="appointmentlist">
                    <div class="row">
                        <div class="col-md-5 appointment_grid_list">
                            <?php
                            $rr=0;
                            $appointmentdata = [];
                            $appointmentlists = \App\Models\BookingAppointment::where('client_id', $fetchedData->id)->orderby('created_at', 'DESC')->get();

                            $appointmentlistslast = \App\Models\BookingAppointment::where('client_id', $fetchedData->id)->orderby('created_at', 'DESC')->first();
                            foreach($appointmentlists as $appointmentlist){
                                $admin = \App\Models\Admin::select('id', 'first_name','email')->where('id', $appointmentlist->user_id)->first();
                                $first_name= $admin->first_name ?? 'N/A';
                                $datetime = $appointmentlist->created_at;
                                $timeago = \App\Http\Controllers\Controller::time_elapsed_string($datetime);

                                $appointmentdata[$appointmentlist->id] = [
                                    'title' => $appointmentlist->service_type,
                                    'time' => date('H:i A', strtotime($appointmentlist->timeslot_full)),
                                    'date' => date('d/m/Y', strtotime($appointmentlist->appointment_datetime)),
                                    'description' => htmlspecialchars($appointmentlist->enquiry_details, ENT_QUOTES, 'UTF-8'),
                                    'createdby' => substr($first_name, 0, 1),
                                    'createdname' => $first_name,
                                    'createdemail' => $admin->email ?? 'N/A',
                                ];
                            ?>

                            <div class="appointmentdata <?php if($rr == 0){ echo 'active'; } ?>" data-id="<?php echo $appointmentlist->id; ?>">
                                <div class="appointment_col">
                                    <div class="appointdate">
                                        <h5><?php echo date('d/m/Y', strtotime($appointmentlist->appointment_datetime)); ?></h5>
                                        <p><?php echo date('H:i A', strtotime($appointmentlist->timeslot_full)); ?><br>
                                        <i><small><?php echo $timeago ?></small></i></p>
                                    </div>
                                    <div class="title_desc">
                                        <h5><?php echo $appointmentlist->service_type; ?></h5>
                                        <p><?php echo $appointmentlist->enquiry_details; ?></p>
                                    </div>
                                    <div class="appoint_created">
                                        <span class="span_label">Created By:
                                        <span><?php echo substr($first_name, 0, 1); ?></span></span>
                                        <!--<div class="dropdown d-inline dropdown_ellipsis_icon">
                                            <a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                            <div class="dropdown-menu">
                                                <a data-id="{{$appointmentlist->id}}" data-href="deleteappointment" class="dropdown-item deletenote" href="javascript:;" >Delete</a>
                                            </div>
                                        </div>-->
                                    </div>
                                </div>
                            </div>
                            <?php $rr++; } ?>
                        </div>
                        <div class="col-md-7">
                            <div class="editappointment">
                            @if($appointmentlistslast)
                                <?php
                                $adminfirst = \App\Models\Admin::select('id', 'first_name','email')->where('id', @$appointmentlistslast->user_id)->first();
                                ?>
                                <div class="content">
                                    <h4 class="appointmentname"><?php echo @$appointmentlistslast->service_type; ?></h4>
                                    <div class="appitem">
                                        <i class="fa fa-clock"></i>
                                        <span class="appcontent appointmenttime"><?php echo date('H:i A', strtotime(@$appointmentlistslast->timeslot_full)); ?></span>
                                    </div>
                                    <div class="appitem">
                                        <i class="fa fa-calendar"></i>
                                        <span class="appcontent appointmentdate"><?php echo date('d/m/Y', strtotime(@$appointmentlistslast->appointment_datetime)); ?></span>
                                    </div>
                                    <div class="description appointmentdescription">
                                        <p><?php echo @$appointmentlistslast->enquiry_details; ?></p>
                                    </div>
                                    <div class="created_by">
                                        <span class="label">Created By:</span>
                                        <div class="createdby">
                                            <span class="appointmentcreatedby"><?php echo substr(@$adminfirst->first_name, 0, 1); ?></span>
                                        </div>
                                        <div class="createdinfo">
                                            <a href="" class="appointmentcreatedname"><?php echo @$adminfirst->first_name ?></a>
                                            <p class="appointmentcreatedemail"><?php echo @$adminfirst->primary_email; ?></p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

