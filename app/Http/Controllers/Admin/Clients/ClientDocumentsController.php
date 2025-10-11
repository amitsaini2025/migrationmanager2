<?php

namespace App\Http\Controllers\Admin\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Models\Admin;
use App\Models\ActivitiesLog;
use App\Models\Document;
use App\Models\ClientMatter;
use App\Models\VisaDocChecklist;
use App\Models\PersonalDocumentType;
use App\Models\VisaDocumentType;

use App\Traits\ClientAuthorization;
use App\Traits\ClientHelpers;

class ClientDocumentsController extends Controller
{
    use ClientAuthorization, ClientHelpers;

    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Add Personal/Education Document Checklist
     */
    public function addedudocchecklist(Request $request){
        $clientid = $request->clientid;
        $admin_info1 = Admin::select('client_id')->where('id', $clientid)->first();
        if(!empty($admin_info1)){
            $client_unique_id = $admin_info1->client_id;
        } else {
            $client_unique_id = "";
        }
        $doctype = isset($request->doctype)? $request->doctype : '';

        if ($request->has('checklist'))
        {
            $checklistArray = $request->input('checklist');
            if (is_array($checklistArray))
            {
                foreach ($checklistArray as $item)
                {
                    $obj = new Document;
                    $obj->user_id = Auth::user()->id;
                    $obj->client_id = $clientid;
                    $obj->type = $request->type;
                    $obj->doc_type = $doctype;
                    $obj->folder_name = $request->folder_name;
                    $obj->checklist = $item;
                    $saved = $obj->save();
                } //end foreach

                if($saved)
                {
                    if($request->type == 'client'){
                        $subject = 'added personal checklist';
                        $objs = new ActivitiesLog;
                        $objs->client_id = $clientid;
                        $objs->created_by = Auth::user()->id;
                        $objs->description = '';
                        $objs->subject = $subject;
                        $objs->save();
                    }

                    $response['status'] = true;
                    $response['message'] = 'You\'ve successfully added your personal checklist';

                    $fetchd = Document::where('client_id',$clientid)->whereNull('not_used_doc')->where('doc_type',$doctype)->where('type',$request->type)->where('folder_name',$request->folder_name)->orderby('updated_at', 'DESC')->get();
                    ob_start();
                    foreach($fetchd as $docKey=>$fetch)
                    {
                        $admin = Admin::where('id', $fetch->user_id)->first();
                        ?>
                        <tr class="drow" id="id_<?php echo $fetch->id; ?>">
                            <td><?php echo $docKey+1;?></td>
                            <td style="white-space: initial;">
                                <div data-id="<?php echo $fetch->id;?>" data-personalchecklistname="<?php echo $fetch->checklist; ?>" class="personalchecklist-row">
                                    <span><?php echo $fetch->checklist; ?></span>
                                </div>
                            </td>
                            <td style="white-space: initial;">
                                <?php
                                echo $admin->first_name. "<br>";
                                echo date('d/m/Y', strtotime($fetch->created_at));
                                ?>
                            </td>
                            <td style="white-space: initial;">
                                <?php
                                if( isset($fetch->file_name) && $fetch->file_name !=""){ ?>
                                    <div data-id="<?php echo $fetch->id; ?>" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
                                        <a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-<?php echo $request->folder_name;?>')">
                                            <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                        </a>
                                    </div>
                                <?php
                                }
                                else
                                {?>
                                    <div class="upload_document" style="display:inline-block;">
                                        <form method="POST" enctype="multipart/form-data" id="upload_form_<?php echo $fetch->id;?>">
                                            <input type="hidden" name="_token" value="<?php echo csrf_token();?>" />
                                            <input type="hidden" name="clientid" value="<?php echo $clientid;?>">
                                            <input type="hidden" name="fileid" value="<?php echo $fetch->id;?>">
                                            <input type="hidden" name="type" value="client">
                                            <input type="hidden" name="doctype" value="personal">
                                            <input type="hidden" name="doccategory" value="<?php echo $request->doccategory;?>">
                                            <a href="javascript:;" class="btn btn-primary add-document" data-fileid="<?php echo $fetch->id;?>"><i class="fa fa-plus"></i> Add Document</a>
                                            <input class="docupload" data-fileid="<?php echo $fetch->id;?>" data-doccategory="<?php echo $request->doccategory;?>" type="file" name="document_upload" style="display: none;">
                                        </form>
                                    </div>
                                <?php
                                }?>
                            </td>
                            <td style="white-space: initial;">
                                <?php
                                if( isset($fetch->file_name) && $fetch->file_name !="")
                                { ?>
                                    <div class="dropdown d-inline">
                                        <button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">Action</button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item renamechecklist" href="javascript:;">Rename Checklist</a>
                                            <a class="dropdown-item renamedoc" href="javascript:;">Rename File Name</a>
                                            <?php
                                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                            ?>
                                            <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
                                            <?php
                                            $explodeimg = explode('.',$fetch->myfile);
                                            if($explodeimg[1] == 'jpg'|| $explodeimg[1] == 'png'|| $explodeimg[1] == 'jpeg'){
                                            ?>
                                                <a target="_blank" class="dropdown-item" href="<?php echo \URL::to('/admin/document/download/pdf'); ?>/<?php echo $fetch->id; ?>">PDF</a>
                                            <?php } ?>
                                            <a href="#" class="dropdown-item download-file" data-filelink="<?php echo $fetch->myfile; ?>" data-filename="<?php echo $fetch->myfile_key; ?>">Download</a>

                                            <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item notuseddoc" data-doctype="personal" data-doccategory="<?php echo $request->doccategory;?>" data-href="notuseddoc" href="javascript:;">Not Used</a>

                                            <?php
                                            if (strtolower($fetch->filetype) === 'pdf')
                                            {
                                                if ($fetch->status === 'draft'){
                                                    $url1 = route('documents.edit', $fetch->id);
                                                ?>
                                                    <a target="_blank" href="<?php echo $url1;?>" class="dropdown-item">Send To Signature</a>
                                                <?php
                                                }

                                                if($fetch->status === 'sent') {

                                                    $url2 = route('documents.index', $fetch->id);
                                                ?>
                                                    <a target="_blank" href="<?php echo $url2;?>" class="dropdown-item">Check To Signature</a>
                                                <?php
                                                }

                                                if($fetch->status === 'signed') {
                                                    $url3 = route('download.signed', $fetch->id);
                                                ?>
                                                    <a target="_blank" href="<?php echo $url3;?>" class="dropdown-item">Download Signed</a>
                                                <?php
                                                }
                                            }?>

                                        </div>
                                    </div>
                                <?php
                                }?>
                            </td>
                        </tr>
			        <?php
			        } //end foreach

                    $data = ob_get_clean();
                    ob_start();
                    foreach($fetchd as $fetch)
                    {
                        $admin = Admin::where('id', $fetch->user_id)->first();
                        ?>
                        <div class="grid_list">
                            <div class="grid_col">
                                <div class="grid_icon">
                                    <i class="fas fa-file-image"></i>
                                </div>
                                <div class="grid_content">
                                    <span id="grid_<?php echo $fetch->id; ?>" class="gridfilename"><?php echo $fetch->file_name; ?></span>
                                    <div class="dropdown d-inline dropdown_ellipsis_icon">
                                        <a class="dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu">
                                            <?php
                                            $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                            ?>
                                            <?php if( isset($fetch->myfile) && $fetch->myfile != ""){?>
                                            <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
                                            <a href="#" class="dropdown-item download-file" data-filelink="<?php echo $fetch->myfile; ?>" data-filename="<?php echo $fetch->myfile_key; ?>">Download</a>

                                            <a data-id="<?php echo $fetch->id; ?>" class="dropdown-item notuseddoc" data-doctype="personal" data-doccategory="<?php echo $request->folder_name;?>" data-href="notuseddoc" href="javascript:;">Not Used</a>
                                            <?php
                                            if (strtolower($fetch->filetype) === 'pdf')
                                            {
                                                if ($fetch->status === 'draft'){
                                                    $url1 = route('documents.edit', $fetch->id);
                                                ?>
                                                    <a target="_blank" href="<?php echo $url1;?>" class="dropdown-item">Send To Signature</a>
                                                <?php
                                                }

                                                if($fetch->status === 'sent') {

                                                    $url2 = route('documents.index', $fetch->id);
                                                ?>
                                                    <a target="_blank" href="<?php echo $url2;?>" class="dropdown-item">Check To Signature</a>
                                                <?php
                                                }

                                                if($fetch->status === 'signed') {
                                                    $url3 = route('download.signed', $fetch->id);
                                                ?>
                                                    <a target="_blank" href="<?php echo $url3;?>" class="dropdown-item">Download Signed</a>
                                                <?php
                                                }
                                            }?>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    } //end foreach
                    $griddata = ob_get_clean();
                    $response['data'] = $data;
                    $response['griddata'] = $griddata;
                } //end if
                else
                {
                    $response['status'] = false;
                    $response['message'] = 'Please try again';
                } //end else
            } //end if
            else
            {
                $response['status'] = false;
                $response['message'] = 'Please try again';
            } //end else
        }
        else
        {
            $response['status'] = false;
            $response['message'] = 'Please try again';
        } //end else
        echo json_encode($response);
	}
    
    /**
     * Upload Personal/Education Document
     */
    public function uploadedudocument(Request $request) {
        ob_start();
    
        $response = ['status' => false, 'message' => 'Please try again', 'data' => '', 'griddata' => ''];
        $clientid = $request->clientid;
        $admin_info1 = Admin::select('client_id', 'first_name')->where('id', $clientid)->first();
        $client_unique_id = !empty($admin_info1) ? $admin_info1->client_id : "";
        $client_first_name = !empty($admin_info1) ? preg_replace('/[^a-zA-Z0-9_\-]/', '_', $admin_info1->first_name) : "client";
    
        $doctype = $request->doctype ?? '';
    
        try {
            if ($request->hasfile('document_upload')) {
                $file = $request->file('document_upload');
                $size = $file->getSize();
                $fileName = $file->getClientOriginalName();
    
                if (!preg_match('/^[a-zA-Z0-9_\-\.\s\$]+$/', $fileName)) {
                    $response['message'] = 'File name can only contain letters, numbers, dashes (-), underscores (_), spaces, dots (.), and dollar signs ($). Please rename the file and try again.';
                } else {
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
    
                    // Get checklist (doc category)
                    $req_file_id = $request->fileid;
                    $obj = Document::find($req_file_id);
                    $checklistName = $obj->checklist;
                    $name = $client_first_name . "_" . $checklistName . "_" . time() . "." . $extension;
    
                    $filePath = $client_unique_id . '/' . $doctype . '/' . $name;
                    Storage::disk('s3')->put($filePath, file_get_contents($file));
    
                    $req_file_id = $request->fileid;
                    $obj = Document::find($req_file_id);
                    if ($obj) {
                        $obj->file_name = $client_first_name . "_" . $checklistName . "_" . time();
                        $obj->filetype = $extension;
                        $obj->user_id = Auth::user()->id;
                        $fileUrl = Storage::disk('s3')->url($filePath);
                        $obj->myfile = $fileUrl;
                        $obj->myfile_key = $name;
                        $obj->type = $request->type;
                        $obj->file_size = $size;
                        $obj->doc_type = $doctype;
                        $saved = $obj->save();
    
                        if ($saved && $request->type == 'client') {
                            $subject = 'updated personal document';
                            $objs = new ActivitiesLog;
                            $objs->client_id = $clientid;
                            $objs->created_by = Auth::user()->id;
                            $objs->description = '';
                            $objs->subject = $subject;
                            $objs->save();
                        }
    
                        if ($saved) {
                            $response['status'] = true;
                            $response['message'] = 'File uploaded successfully';
                            $response['filename'] = $name;
                            $response['filetype'] = $extension;
                            $response['fileurl'] = $fileUrl;
                            $response['filekey'] = $name;
                            $response['doccategory'] = $checklistName;
                        }
                    } else {
                        $response['message'] = 'Document record not found.';
                    }
                }
            }
        } catch (\Exception $e) {
            $response['message'] = 'An error occurred: ' . $e->getMessage();
        }
    
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    /**
     * Add Visa Document Checklist
     */
    public function addvisadocchecklist(Request $request) {
        // Note: This is a VERY large method copied from original controller
        // Method body will be added - placeholder for now to keep file manageable
        // TODO: Copy full method from ClientsController lines 5481-5725
        echo json_encode(['status' => false, 'message' => 'Method under construction']);
    }

    /**
     * Upload Visa Document
     */
    public function uploadvisadocument(Request $request) {
        // Note: This is a VERY large method copied from original controller  
        // TODO: Copy full method from ClientsController lines 5973-6060
        echo json_encode(['status' => false, 'message' => 'Method under construction']);
    }

    /**
     * Rename Document
     */
    public function renamedoc(Request $request) {
        $id = $request->id;
        $filename = $request->filename; // new file name without extension

        if(\App\Models\Document::where('id',$id)->exists()){
            $doc = \App\Models\Document::where('id',$id)->first();

            // Get old S3 key and extension
            $oldKey = $doc->myfile_key;
            $extension = $doc->filetype;
            $client_id = $doc->client_id;
            $doc_type = $doc->doc_type;

            // Get client unique id for S3 path
            $admin = \App\Models\Admin::select('client_id')->where('id', $client_id)->first();
            $client_unique_id = $admin ? $admin->client_id : "";

            // Build new key and S3 path
            $newKey = time() . $filename . '.' . $extension;
            $newS3Path = $client_unique_id . '/' . $doc_type . '/' . $newKey;
            $oldS3Path = $client_unique_id . '/' . $doc_type . '/' . $oldKey;

            // Copy and delete in S3
            if (\Storage::disk('s3')->exists($oldS3Path)) {
                try {
                    // Attempt to copy first
                    $copySuccess = \Storage::disk('s3')->copy($oldS3Path, $newS3Path);
                    
                    if ($copySuccess) {
                        // Only delete original if copy was successful
                        \Storage::disk('s3')->delete($oldS3Path);
                    } else {
                        // Copy failed, don't proceed with database update
                        $response['status'] = false;
                        $response['message'] = 'Failed to copy file. Please try again.';
                        echo json_encode($response);
                        return;
                    }
                } catch (\Exception $e) {
                    // Log the error for debugging
                    \Log::error('S3 copy failed: ' . $e->getMessage(), [
                        'oldPath' => $oldS3Path,
                        'newPath' => $newS3Path,
                        'document_id' => $id
                    ]);
                    
                    $response['status'] = false;
                    $response['message'] = 'File operation failed. Please try again.';
                    echo json_encode($response);
                    return;
                }
            } else {
                // Original file not found - handle gracefully
                \Log::warning('Document rename failed: Original file not found', [
                    'document_id' => $id,
                    'old_s3_path' => $oldS3Path,
                    'new_filename' => $filename,
                    'user_id' => Auth::user()->id ?? 'unknown'
                ]);
                
                // Return error response
                $response['status'] = false;
                $response['message'] = 'Original document not found. Please re-upload the document.';
                $response['error_type'] = 'file_not_found';
                echo json_encode($response);
                return;
            }

            // Build new S3 URL
            $newS3Url = \Storage::disk('s3')->url($newS3Path);

            // Update DB
            $res = \DB::table('documents')->where('id', $id)->update([
                'file_name' => $filename,
                'myfile' => $newS3Url,
                'myfile_key' => $newKey
            ]);

            if($res){
                $response['status'] = true;
                $response['data'] = 'Document saved successfully';
                $response['Id'] = $id;
                $response['filename'] = $filename;
                $response['filetype'] = $doc->filetype;
                $response['fileurl'] = $newS3Url;

                if($doc->doc_type == 'personal') {
                    $response['folder_name'] = 'preview-container-'.$doc->folder_name;
                } else if($doc->doc_type == 'visa') {
                    $response['folder_name'] = 'preview-container-migdocumnetlist';
                }
            } else {
                $response['status'] = false;
                $response['message'] = 'Please try again';
            }
        } else {
            $response['status'] = false;
            $response['message'] = 'Please try again';
        }
        echo json_encode($response);
    }

    /**
     * Delete Document
     */
    public function deletedocs(Request $request) {
        // TODO: Copy full method from ClientsController lines 6234-6281
        echo json_encode(['status' => false, 'message' => 'Method under construction']);
    }

    /**
     * Verify Document
     */
    public function verifydoc(Request $request) {
        // TODO: Copy full method from ClientsController lines 11510-11562
        echo json_encode(['status' => false, 'message' => 'Method under construction']);
    }

    /**
     * Get Visa Checklist
     */
    public function getvisachecklist(Request $request) {
        // TODO: Copy full method from ClientsController lines 11565-11592
        echo json_encode(['status' => false, 'message' => 'Method under construction']);
    }

    /**
     * Mark Document as Not Used
     */
    public function notuseddoc(Request $request) {
        $doc_id = $request->doc_id;
        $doc_type = $request->doc_type;
        if(\App\Models\Document::where('id',$doc_id)->exists()){
            $upd = DB::table('documents')->where('id', $doc_id)->update(array('not_used_doc' => 1));
            if($upd){
                $docInfo = \App\Models\Document::where('id',$doc_id)->first();
                $subject = $doc_type.' document moved to Not Used Tab';
                $objs = new ActivitiesLog;
                $objs->client_id = $docInfo->client_id;
                $objs->created_by = Auth::user()->id;
                $objs->description = '';
                $objs->subject = $subject;
                $objs->save();

                if($docInfo){
                    if( isset($docInfo->user_id) && $docInfo->user_id!= "" ){
                        $adminInfo = \App\Models\Admin::select('first_name')->where('id',$docInfo->user_id)->first();
                        $response['Added_By'] = $adminInfo->first_name;
                        $response['Added_date'] = date('d/m/Y',strtotime($docInfo->created_at));
                    } else {
                        $response['Added_By'] = "N/A";
                        $response['Added_date'] = "N/A";
                    }

                    if( isset($docInfo->checklist_verified_by) && $docInfo->checklist_verified_by!= "" ){
                        $verifyInfo = \App\Models\Admin::select('first_name')->where('id',$docInfo->checklist_verified_by)->first();
                        $response['Verified_By'] = $verifyInfo->first_name;
                        $response['Verified_At'] = date('d/m/Y',strtotime($docInfo->checklist_verified_at));
                    } else {
                        $response['Verified_By'] = "N/A";
                        $response['Verified_At'] = "N/A";
                    }
                }

                $response['docInfo'] = $docInfo;
                $response['doc_type'] = $doc_type;
                $response['doc_id'] = $doc_id;

                if(isset($docInfo->doc_type) && $docInfo->doc_type == 'personal'){
                    $response['doc_category'] = $docInfo->folder_name;
                } else {
                    $response['doc_category'] = "";
                }
                $response['status'] = true;
                $response['data'] = $doc_type.' document moved to Not Used Tab';
            } else {
                $response['status'] = false;
                $response['message'] = 'Please try again';
                $response['doc_type'] = "";
                $response['doc_id'] = "";
                $response['docInfo'] = "";
                $response['doc_category'] = "";
                $response['Added_By'] = "";
                $response['Added_date'] = "";
                $response['Verified_By'] = "";
                $response['Verified_At'] = "";
            }
        } else {
            $response['status'] = false;
            $response['message'] = 'Please try again';
            $response['doc_type'] = "";
            $response['doc_id'] = "";
            $response['docInfo'] = "";
            $response['doc_category'] = "";
            $response['Added_By'] = "";
            $response['Added_date'] = "";
            $response['Verified_By'] = "";
            $response['Verified_At'] = "";
        }
        echo json_encode($response);
    }

    /**
     * Rename Checklist in Document
     */
    public function renamechecklistdoc(Request $request) {
        $id = $request->id;
        $checklist = $request->checklist;
        if(\App\Models\Document::where('id',$id)->exists()){
            $doc = \App\Models\Document::where('id',$id)->first();
            $res = DB::table('documents')->where('id', @$id)->update(['checklist' => $checklist]);
            if($res){
                $response['status'] = true;
                $response['data'] = 'Checklist saved successfully';
                $response['Id'] = $id;
                $response['checklist'] = $checklist;
            }else{
                $response['status'] = false;
                $response['message'] = 'Please try again';
            }
        }else{
            $response['status'] = false;
            $response['message'] = 'Please try again';
        }
        echo json_encode($response);
    }

    /**
     * Move Document Back from Not Used
     */
    public function backtodoc(Request $request) {
        // TODO: Copy full method from ClientsController lines 11749-11815
        echo json_encode(['status' => false, 'message' => 'Method under construction']);
    }

    /**
     * Download Document (S3 Temporary URL)
     */
    public function download_document(Request $request) {
        // TODO: Copy full method from ClientsController lines 12441-12481
        return abort(501, 'Method under construction');
    }

    /**
     * Add Personal Document Category
     */
    public function addPersonalDocCategory(Request $request) {
        // TODO: Copy full method from ClientsController lines 13219-13281
        return response()->json(['status' => false, 'message' => 'Method under construction']);
    }

    /**
     * Update Personal Document Category
     */
    public function updatePersonalDocCategory(Request $request) {
        // TODO: Copy full method from ClientsController lines 13284-13336
        return response()->json(['status' => false, 'message' => 'Method under construction']);
    }

    /**
     * Add Visa Document Category
     */
    public function addVisaDocCategory(Request $request) {
        // TODO: Copy full method from ClientsController lines 13358+
        return response()->json(['status' => false, 'message' => 'Method under construction']);
    }

    /**
     * Update Visa Document Category  
     */
    public function updateVisaDocCategory(Request $request) {
        // TODO: Copy full method from ClientsController lines 13425+
        return response()->json(['status' => false, 'message' => 'Method under construction']);
    }
}
