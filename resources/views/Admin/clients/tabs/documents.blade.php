           <!-- Documents Tab -->
           <div class="tab-pane" id="documentalls-tab">
                <div class="card full-width documentalls-container">
                    <!-- Subtabs Navigation -->
                    <nav class="subtabs">
                        <button class="subtab-button active" data-subtab="documents">Personal Document</button>
                        <?php
                        $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count();
                        //dd($matter_cnt);
                        if($matter_cnt >0 ) { ?>
                            <button class="subtab-button" data-subtab="migrationdocuments">Visa Document</button>
                        <?php
                        }
                        ?>
                        <button class="subtab-button ml-auto" data-subtab="notuseddocuments">Not Used Documents</button>
                    </nav>

                    <!-- Subtab Contents -->
                    <div class="subtab-content" id="subtab-content">

                        <?php
                        $clientId = $fetchedData->id ?? null;
                        $persDocCatList = \App\Models\PersonalDocumentType::select('id', 'title','client_id')
                            ->where('status', 1)
                            ->where(function($query) use ($clientId) {
                                $query->whereNull('client_id')
                                    ->orWhere('client_id', $clientId);
                            })
                            ->orderBy('id', 'ASC')
                            ->get();
                        ?>

                        <!-- Personal Documents Subtab -->
                        <div class="subtab-pane active" id="documents-subtab">
                            <!-- Document Type Subtabs Container -->
                            <div class="subtab-header-container">
                                <nav class="subtabs2" style="margin: 10px 0 0 10px; display: inline-block; flex-grow: 1;">
                                    <?php foreach ($persDocCatList as $catVal): ?>
                                        <?php
                                        $id = $catVal->id;
                                        $isActive = $id == 1 ? 'active' : '';
                                        $isClientGenerated = $catVal->client_id !== null;
                                        ?>
                                        <div style="display: inline-block; position: relative;" class="button-container">
                                            <button class="subtab2-button <?= $isActive ?>" data-subtab2="<?= $id ?>" style="color: #000 !important;">
                                                <?= htmlspecialchars($catVal->title) ?>
                                            </button>
                                            <?php if ($isClientGenerated): ?>
                                                <div class="action-buttons" style="display: none; position: absolute; top: 0; right: -8px;">
                                                    <button class="btn btn-sm btn-warning update-personal-cat-title" data-id="<?= $id ?>" style="padding: 2px 0px 2px 6px;"><i class="fa fa-edit" aria-hidden="true"></i></button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </nav>
                                <button style="margin-top: 10px;" class="btn add_personal_doc_cat-btn add_personal_doc_cat" data-type="personal" data-categoryid="">
                                    <i class="fas fa-plus"></i> Add Personal Document Category
                                </button>
                            </div>

                            <!-- Subtab2 Contents -->
                            <div class="subtab2-content">
                                <?php foreach ($persDocCatList as $catVal): ?>
                                    <?php
                                    $id = $catVal->id;
                                    $isActive = $id == 1 ? 'active' : '';
                                    $folderName = $id;
                                    ?>

                                    <div class="subtab2-pane <?= $isActive ?>" id="<?= $id ?>-subtab2">
                                        <div class="checklist-table-container" style="vertical-align: top; margin-top: 10px; width: 760px;">
                                            <div class="subtab2-header" style="margin-left: 10px;">
                                                <h3><i class="fas fa-file-alt"></i> <?= htmlspecialchars($catVal->title) ?> Documents</h3>
                                                <button class="btn add-checklist-btn add_education_doc" data-type="personal" data-categoryid="<?= $id ?>">
                                                    <i class="fas fa-plus"></i> Add Personal Checklist
                                                </button>
                                            </div>
                                            <table class="checklist-table">
                                                <thead>
                                                    <tr>
                                                        <th>SNo.</th>
                                                        <th>Checklist</th>
                                                        <th>Added By</th>
                                                        <th>File Name</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="tdata persdocumnetlist documnetlist_<?= $id ?>">
                                                    <?php
                                                    $documents = \App\Models\Document::where('client_id', $clientId)
                                                        ->whereNull('not_used_doc')
                                                        ->where('doc_type', 'personal')
                                                        ->where('folder_name', $folderName)
                                                        ->where('type', 'client')
                                                        ->orderBy('created_at', 'DESC')
                                                        ->get();
                                                    ?>
                                                    <?php foreach ($documents as $docKey => $fetch): ?>
                                                        <?php
                                                        $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                                                        $fileUrl = $fetch->myfile_key
                                                            ? asset($fetch->myfile)
                                                            : 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . $clientId . '/personal/' . $fetch->myfile;
                                                        ?>
                                                        <tr class="drow" id="id_<?= $fetch->id ?>">
                                                            <td><?= $docKey + 1 ?></td>
                                                            <td style="white-space: initial;">
                                                                <div data-id="<?= $fetch->id ?>" data-personalchecklistname="<?= htmlspecialchars($fetch->checklist) ?>" class="personalchecklist-row">
                                                                    <span><?= htmlspecialchars($fetch->checklist) ?></span>
                                                                </div>
                                                            </td>
                                                            <td style="white-space: initial;">
                                                                <?= htmlspecialchars($admin->first_name ?? 'NA') ?><br>
                                                                <?= date('d/m/Y', strtotime($fetch->created_at)) ?>
                                                            </td>
                                                            <td style="white-space: initial;">
                                                                <?php if ($fetch->file_name): ?>
                                                                    <div data-id="<?= $fetch->id ?>" data-name="<?= htmlspecialchars($fetch->file_name) ?>" class="doc-row">
                                                                        <a href="javascript:void(0);" onclick="previewFile('<?= $fetch->filetype ?>','<?= $fileUrl ?>','preview-container-<?= $id ?>')">
                                                                            <i class="fas fa-file-image"></i> <span><?= htmlspecialchars($fetch->file_name . '.' . $fetch->filetype) ?></span>
                                                                        </a>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div class="upload_document" style="display: inline-block;">
                                                                        <form method="POST" enctype="multipart/form-data" id="upload_form_<?= $fetch->id ?>">
                                                                            @csrf
                                                                            <input type="hidden" name="clientid" value="<?= $clientId ?>">
                                                                            <input type="hidden" name="fileid" value="<?= $fetch->id ?>">
                                                                            <input type="hidden" name="type" value="client">
                                                                            <input type="hidden" name="doctype" value="personal">
                                                                            <input type="hidden" name="doccategory" value="<?= $catVal->title ?>">

                                                                            <a href="javascript:;" class="btn btn-primary add-document" data-fileid="<?= $fetch->id ?>"><i class="fa fa-plus"></i> Add Document</a>
					                                                        <input class="docupload" data-fileid="<?= $fetch->id ?>" data-doccategory="<?= $id ?>" type="file" name="document_upload"  style="display: none;">
                                                                        </form>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($fetch->myfile): ?>
                                                                    <div class="dropdown d-inline">
                                                                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Action</button>
                                                                        <div class="dropdown-menu">
                                                                            <a class="dropdown-item renamechecklist" href="javascript:;">Rename Checklist</a>
                                                                            <a class="dropdown-item renamedoc" href="javascript:;">Rename File Name</a>
                                                                            <a target="_blank" class="dropdown-item" href="<?= $fetch->myfile ?>">Preview</a>
                                                                            <?php
                                                                            $fileExt = pathinfo($fetch->myfile, PATHINFO_EXTENSION);
                                                                            if (in_array($fileExt, ['jpg', 'png', 'jpeg'])): ?>
                                                                                <a target="_blank" class="dropdown-item" href="<?= URL::to('/admin/document/download/pdf') ?>/<?= $fetch->id ?>">PDF</a>
                                                                            <?php endif; ?>
                                                                            <a href="#" class="dropdown-item download-file" data-filelink="<?= $fetch->myfile ?>" data-filename="<?= $fetch->myfile_key ?>">Download</a>
                                                                            <a data-id="<?= $fetch->id ?>" class="dropdown-item notuseddoc" data-doctype="personal" data-doccategory="<?= $catVal->title ?>" data-href="notuseddoc" href="javascript:;">Not Used</a>

                                                                            @if (strtolower($fetch->filetype) === 'pdf')
                                                                                @if ($fetch->status === 'draft')
                                                                                    <form method="GET" action="{{ route('documents.edit', $fetch->id) }}" target="_blank" style="display: inline;">
                                                                                        <button type="submit" class="dropdown-item" style="background: none; border: none; width: 100%; text-align: left; padding: 0.25rem 1.5rem;">
                                                                                            Send To Signature
                                                                                        </button>
                                                                                    </form>
                                                                                @endif

                                                                                @if($fetch->status === 'sent')
                                                                                    <form method="GET" action="{{ route('documents.index', $fetch->id) }}" target="_blank" style="display: inline;">
                                                                                        <button type="submit" class="dropdown-item" style="background: none; border: none; width: 100%; text-align: left; padding: 0.25rem 1.5rem;">
                                                                                            Check To Signature
                                                                                        </button>
                                                                                    </form>
                                                                                @endif

                                                                                @if($fetch->status === 'signed')
                                                                                    <a target="_blank" href="{{ route('download.signed', $fetch->id) }}" class="dropdown-item">Download Signed</a>
                                                                                @endif

                                                                            @endif


                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="grid_data griddata_<?= $id ?>">
                                            <?php foreach ($documents as $fetch): ?>
                                                <?php if ($fetch->myfile): ?>
                                                    <div class="grid_list" id="gid_<?= $fetch->id ?>">
                                                        <div class="grid_col">
                                                            <div class="grid_icon">
                                                                <i class="fas fa-file-image"></i>
                                                            </div>
                                                            <div class="grid_content">
                                                                <span id="grid_<?= $fetch->id ?>" class="gridfilename"><?= htmlspecialchars($fetch->file_name) ?></span>
                                                                <div class="dropdown d-inline dropdown_ellipsis_icon">
                                                                    <a class="dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                                                    <div class="dropdown-menu">
                                                                        <a target="_blank" class="dropdown-item" href="<?= $fetch->myfile ?>">Preview</a>
                                                                        <a href="#" class="dropdown-item download-file" data-filelink="<?= $fetch->myfile ?>" data-filename="<?= $fetch->myfile_key ?>">Download</a>
                                                                        <a data-id="<?= $fetch->id ?>" class="dropdown-item notuseddoc" data-doctype="personal" data-doccategory="<?= $catVal->title ?>" data-href="notuseddoc" href="javascript:;">Not Used</a>

                                                                         @if (strtolower($fetch->filetype) === 'pdf')

                                                                                @if ($fetch->status === 'draft')
                                                                                    <form method="GET" action="{{ route('documents.edit', $fetch->id) }}" target="_blank" style="display: inline;">
                                                                                        <button type="submit" class="dropdown-item" style="background: none; border: none; width: 100%; text-align: left; padding: 0.25rem 1.5rem;">
                                                                                            Send To Signature
                                                                                        </button>
                                                                                    </form>
                                                                                @endif

                                                                                @if($fetch->status === 'sent')
                                                                                    <form method="GET" action="{{ route('documents.index', $fetch->id) }}" target="_blank" style="display: inline;">
                                                                                        <button type="submit" class="dropdown-item" style="background: none; border: none; width: 100%; text-align: left; padding: 0.25rem 1.5rem;">
                                                                                            Check To Signature
                                                                                        </button>
                                                                                    </form>
                                                                                @endif

                                                                                @if($fetch->status === 'signed')
                                                                                    <a target="_blank" href="{{ route('admin.download.signed', $fetch->id) }}" class="dropdown-item">Download Signed</a>
                                                                                @endif

                                                                            @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <div class="clearfix"></div>
                                        </div>

                                        <div class="preview-pane file-preview-container preview-container-<?= $id ?>" style="display: inline; margin-top: 15px !important; width: 499px;">
                                            <h3>File Preview</h3>
                                            <p>Click on a file to preview it here.</p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <?php
                        $client_selected_matter_id1 = null;
                        $matter_cnt = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('matter_status',1)->count(); //dd($matter_cnt);
                        if( $matter_cnt >0 ) {
                            //if client unique reference id is present in url
                            if( isset($id1) && $id1 != "") {
                                $matter_get_id = \App\Models\ClientMatter::select('id')->where('client_id',$fetchedData->id)->where('client_unique_matter_no',$id1)->first();
                            } else {
                                $matter_get_id = \App\Models\ClientMatter::select('id')->where('client_id', $fetchedData->id)->orderBy('id', 'desc')->first();
                            } //dd($matter_get_id);
                            if($matter_get_id ){
                                $client_selected_matter_id1 = $matter_get_id->id;
                            }
                        }//dd('client_selected_matter_id==='.$client_selected_matter_id1);

                        $visaDocCatList = \App\Models\VisaDocumentType::select('id', 'title','client_id','client_matter_id')
                        ->where('status', 1)
                        ->where(function($query) use ($client_selected_matter_id1) {
                            $query->whereNull('client_matter_id')
                                ->orWhere('client_matter_id', (int) $client_selected_matter_id1);
                        })
                        ->orderBy('id', 'ASC')
                        ->get();
                        //dd($visaDocCatList);
                        ?>
                        <!-- Visa Documents Subtab -->
                        <div class="subtab-pane" id="migrationdocuments-subtab">
                            <!-- Visa Document Type Subtabs Container -->
                            <div class="subtab-header-container">
                                <nav class="subtabs6" style="margin: 10px 0 0 10px; display: inline-block; flex-grow: 1;">
                                    <?php foreach ($visaDocCatList as $catVal): ?>
                                        <?php
                                        $id = $catVal->id;
                                        $isActive = $id == 1 ? 'active' : '';
                                        $folderName = $id;
                                        $isClientGenerated = $catVal->client_matter_id !== null;
                                        ?>
                                        <div style="display: inline-block; position: relative;" class="button-container">
                                            <button class="subtab6-button <?= $isActive ?>" data-subtab6="<?= $id ?>" style="color: #000 !important;">
                                                <?= htmlspecialchars($catVal->title) ?>
                                            </button>
                                            <?php if ($isClientGenerated): ?>
                                                <div class="action-buttons" style="display: none; position: absolute; top: 0; right: -8px;">
                                                    <button class="btn btn-sm btn-warning update-visa-cat-title" data-id="<?= $id ?>" style="padding: 2px 0px 2px 6px;"><i class="fa fa-edit" aria-hidden="true"></i></button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </nav>

                                <button style="margin-top: 10px;" class="btn add-visa-doc-category-btn add-visa-doc-category" data-type="visa" data-categoryid="">
                                    <i class="fas fa-plus"></i> Add Visa Document Category
                                </button>
                            </div>

                            <!-- Subtab6 Contents -->
                            <div class="subtab6-content">
                                <?php foreach ($visaDocCatList as $catVal):
                                    $id = $catVal->id;
                                    $isActive = $id == 1 ? 'active' : '';
                                    $folderName = $id;
                                    ?>
                                    <div class="subtab6-pane <?= $isActive ?>" id="<?= $id ?>-subtab6">
                                        <div class="checklist-table-container" style="vertical-align: top; margin-top: 10px; width: 760px;">
                                            <div class="subtab6-header" style="margin-left: 10px;">
                                                <h3><i class="fas fa-file-alt"></i> <?= htmlspecialchars($catVal->title) ?> Documents</h3>
                                                <button class="btn add-checklist-btn add_migration_doc" data-type="visa" data-categoryid="<?= $id ?>">
                                                    <i class="fas fa-plus"></i> Add Visa Checklist
                                                </button>
                                            </div>
                                            <table class="checklist-table">
                                                <thead>
                                                    <tr>
                                                        <th>SNo.</th>
                                                        <th>Checklist</th>
                                                        <th>Added By</th>
                                                        <th>File Name</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="tdata migdocumnetlist1 migdocumnetlist_<?= $id ?>">
                                                    <?php
                                                     $documents = \App\Models\Document::where('client_id', $fetchedData->id)
                                                        //->where('client_matter_id', $fetchedData->client_matter_id ?? null)
                                                        ->whereNull('not_used_doc')
                                                        ->where('doc_type', 'visa')
                                                        ->where('folder_name', $folderName)
                                                        ->where('type', 'client')
                                                        ->orderBy('created_at', 'DESC')
                                                        ->get();
                                                    ?>
                                                    <?php foreach ($documents as $visaKey => $fetch): ?>
                                                        <?php
                                                        $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                                                        $fileUrl = $fetch->myfile_key ? asset($fetch->myfile) : 'https://' . env('AWS_BUCKET') . '.s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . $fetchedData->id . '/visa/' . $fetch->myfile;
                                                        ?>
                                                        <tr class="drow" data-matterid="<?= $fetch->client_matter_id ?>" id="id_<?= $fetch->id ?>">
                                                            <td><?= $visaKey + 1 ?></td>
                                                            <td style="white-space: initial;">
                                                                <div data-id="<?= $fetch->id ?>" data-visachecklistname="<?= htmlspecialchars($fetch->checklist) ?>" class="visachecklist-row">
                                                                    <span><?= htmlspecialchars($fetch->checklist) ?></span>
                                                                </div>
                                                            </td>
                                                            <td style="white-space: initial;">
                                                                <?= htmlspecialchars($admin->first_name ?? 'NA') ?><br>
                                                                <?= date('d/m/Y', strtotime($fetch->created_at)) ?>
                                                            </td>
                                                            <td style="white-space: initial;">
                                                                <?php if ($fetch->file_name): ?>
                                                                    <div data-id="<?= $fetch->id ?>" data-name="<?= htmlspecialchars($fetch->file_name) ?>" class="doc-row">
                                                                        <a href="javascript:void(0);" onclick="previewFile('<?= $fetch->filetype ?>','<?= asset($fetch->myfile) ?>','preview-container-migdocumnetlist')">
                                                                            <i class="fas fa-file-image"></i> <span><?= htmlspecialchars($fetch->file_name . '.' . $fetch->filetype) ?></span>
                                                                        </a>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div class="migration_upload_document" style="display: inline-block;">
                                                                        <form method="POST" enctype="multipart/form-data" id="mig_upload_form_<?= $fetch->id ?>">
                                                                            @csrf
                                                                            <input type="hidden" name="clientid" value="<?= $fetchedData->id ?>">
                                                                            <input type="hidden" name="client_matter_id" value="<?= $fetch->client_matter_id ?? '' ?>">
                                                                            <input type="hidden" name="fileid" value="<?= $fetch->id ?>">
                                                                            <input type="hidden" name="type" value="client">
                                                                            <input type="hidden" name="doctype" value="visa">
                                                                            <input type="hidden" name="doccategory" value="<?= $catVal->title ?>">
                                                                            <a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add Document</a>
                                                                            <input class="migdocupload" data-fileid="<?= $fetch->id ?>" data-doccategory="<?= $id ?>" type="file" name="document_upload"/>
                                                                        </form>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($fetch->myfile): ?>
                                                                    <div class="dropdown d-inline">
                                                                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Action</button>
                                                                        <div class="dropdown-menu">
                                                                            <a class="dropdown-item renamechecklist" href="javascript:;">Rename Checklist</a>
                                                                            <a class="dropdown-item renamedoc" href="javascript:;">Rename File Name</a>
                                                                            <a target="_blank" class="dropdown-item" href="<?= $fetch->myfile ?>">Preview</a>
                                                                            <?php $fileExt = pathinfo($fetch->myfile, PATHINFO_EXTENSION); if (in_array($fileExt, ['jpg', 'png', 'jpeg'])): ?>
                                                                                <a target="_blank" class="dropdown-item" href="<?= URL::to('/admin/document/download/pdf') ?>/<?= $fetch->id ?>">PDF</a>
                                                                            <?php endif; ?>
                                                                            <a href="#" class="dropdown-item download-file" data-filelink="<?= $fetch->myfile ?>" data-filename="<?= $fetch->myfile_key ?>">Download</a>
                                                                            <a data-id="<?= $fetch->id ?>" class="dropdown-item notuseddoc" data-doctype="visa" data-href="notuseddoc" href="javascript:;">Not Used</a>

                                                                            @if (strtolower($fetch->filetype) === 'pdf')

                                                                                @if ($fetch->status === 'draft')
                                                                                    <a target="_blank" href="{{ route('documents.edit', $fetch->id) }}" class="dropdown-item">Send To Signature</a>
                                                                                @endif

                                                                                @if($fetch->status === 'sent')
                                                                                    <a target="_blank" href="{{ route('documents.index', $fetch->id) }}" class="dropdown-item">Check To Signature</a>
                                                                                @endif

                                                                                @if($fetch->status === 'signed')
                                                                                    <a target="_blank" href="{{ route('download.signed', $fetch->id) }}" class="dropdown-item">Download Signed</a>
                                                                                @endif

                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="grid_data miggriddata" style="display:none;">
                                            <?php foreach ($visaDocCatList as $catVal):
                                                $id = $catVal->id;
                                                $documents = \App\Models\Document::where('client_id', $fetchedData->id)
                                                    //->where('client_matter_id', $fetchedData->client_matter_id ?? null)
                                                    ->whereNull('not_used_doc')
                                                    ->where('doc_type', 'visa')
                                                    ->where('folder_name', $id)
                                                    ->where('type', 'client')
                                                    ->orderBy('updated_at', 'DESC')
                                                    ->get();
                                                foreach ($documents as $fetch):
                                                    if ($fetch->myfile):
                                                        $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                                                        ?>
                                                        <div class="grid_list" id="gid_<?= $fetch->id ?>">
                                                            <div class="grid_col">
                                                                <div class="grid_icon">
                                                                    <i class="fas fa-file-image"></i>
                                                                </div>
                                                                <div class="grid_content">
                                                                    <span id="grid_<?= $fetch->id ?>" class="gridfilename"><?= htmlspecialchars($fetch->file_name) ?></span>
                                                                    <div class="dropdown d-inline dropdown_ellipsis_icon">
                                                                        <a class="dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                                                        <div class="dropdown-menu">
                                                                            <a target="_blank" class="dropdown-item" href="<?= $fetch->myfile ?>">Preview</a>
                                                                            <a href="#" class="dropdown-item download-file" data-filelink="<?= $fetch->myfile ?>" data-filename="<?= $fetch->myfile_key ?>">Download</a>
                                                                            <a data-id="<?= $fetch->id ?>" class="dropdown-item notuseddoc" data-doctype="visa" data-href="notuseddoc" href="javascript:;">Not Used</a>

                                                                            @if (strtolower($fetch->filetype) === 'pdf')

                                                                                @if ($fetch->status === 'draft')
                                                                                    <form method="GET" action="{{ route('documents.edit', $fetch->id) }}" target="_blank" style="display: inline;">
                                                                                        <button type="submit" class="dropdown-item" style="background: none; border: none; width: 100%; text-align: left; padding: 0.25rem 1.5rem;">
                                                                                            Send To Signature
                                                                                        </button>
                                                                                    </form>
                                                                                @endif

                                                                                @if($fetch->status === 'sent')
                                                                                    <a target="_blank" href="{{ route('documents.index', $fetch->id) }}" class="dropdown-item">Check To Signature</a>
                                                                                @endif

                                                                                @if($fetch->status === 'signed')
                                                                                    <a target="_blank" href="{{ route('download.signed', $fetch->id) }}" class="dropdown-item">Download Signed</a>
                                                                                @endif

                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                <div class="clearfix"></div>
                                            <?php endforeach; ?>
                                        </div>

                                        <div class="preview-pane file-preview-container preview-container-migdocumnetlist" style="display: inline;margin-top: 15px !important; width: 499px;">
                                            <h3>File Preview</h3>
                                            <p>Click on a file to preview it here.</p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Not Used Documents Subtab -->
                        <div class="subtab-pane" id="notuseddocuments-subtab">
                            <div style="display: flex; gap: 20px; padding: 15px;">
                                <!-- Table Container -->
                                <div style="flex: 1; min-width: 0;">
                                    <div class="subtab-header" style="margin-bottom: 15px;">
                                        <h3><i class="fas fa-folder"></i> Not Used Documents</h3>
                                    </div>
                                    <div style="overflow: auto; max-height: calc(100vh - 250px);">
                                        <table class="checklist-table" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th>SNo.</th>
                                                    <th>Checklist</th>
                                                    <th>Document Type</th>
                                                    <th>Added By</th>
                                                    <th>File Name</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody class="tdata notuseddocumnetlist">
                                                <?php
                                                $fetchd = \App\Models\Document::where('client_id', $fetchedData->id)
                                                ->where('not_used_doc', 1)
                                                ->where('type','client')
                                                ->where(function($query) {
                                                    $query->orWhere('doc_type','personal')
                                                    ->orWhere('doc_type','visa');
                                                })->orderBy('type', 'DESC')->get(); //dd($fetchd);
                                                foreach($fetchd as $notuseKey=>$fetch)
                                                {
                                                    $admin = \App\Models\Admin::where('id', $fetch->user_id)->first();
                                                    ?>
                                                    <tr class="drow" id="id_{{$fetch->id}}">
                                                        <td><?php echo $notuseKey+1;?></td>
                                                        <td style="white-space: initial;"><?php echo $fetch->checklist; ?></td>
                                                        <td style="white-space: initial;"><?php echo $fetch->doc_type; ?></td>
                                                        <td style="white-space: initial;">
                                                            <?php
                                                            echo ($admin->first_name ?? 'NA') . "<br>";
                                                            echo date('d/m/Y', strtotime($fetch->created_at));
                                                            ?>
                                                        </td>
                                                        <td style="white-space: initial;">
                                                            <?php
                                                            if( isset($fetch->file_name) && $fetch->file_name !=""){ ?>
                                                                <div data-id="{{$fetch->id}}" data-name="<?php echo $fetch->file_name; ?>" class="doc-row">
                                                                    <?php if( isset($fetch->myfile_key) && $fetch->myfile_key != ""){ //For new file upload ?>
                                                                        <a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($fetch->myfile); ?>','preview-container-notuseddocumnetlist')">
                                                                            <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                                                        </a>
                                                                    <?php } else {  //For old file upload
                                                                        $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                                                        ?>
                                                                        <a href="javascript:void(0);" onclick="previewFile('<?php echo $fetch->filetype;?>','<?php echo asset($myawsfile); ?>','preview-container-notuseddocumnetlist')">
                                                                            <i class="fas fa-file-image"></i> <span><?php echo $fetch->file_name . '.' . $fetch->filetype; ?></span>
                                                                        </a>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php
                                                            }
                                                            else
                                                            {
                                                                echo "N/A";
                                                            }?>
                                                        </td>
                                                        <td>
                                                            <div class="dropdown d-inline">
                                                                <button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">Action</button>
                                                                <div class="dropdown-menu">
                                                                    <?php
                                                                    $url = 'https://'.env('AWS_BUCKET').'.s3.'. env('AWS_DEFAULT_REGION') . '.amazonaws.com/';
                                                                    ?>
                                                                    <?php if( isset($fetch->myfile_key) && $fetch->myfile_key != ""){ //For new file upload ?>
                                                                        <a target="_blank" class="dropdown-item" href="<?php echo $fetch->myfile; ?>">Preview</a>
                                                                    <?php } else {  //For old file upload ?>
                                                                        <a target="_blank" class="dropdown-item" href="<?php echo $url.$fetchedData->client_id.'/'.$fetch->doc_type.'/'.$fetch->myfile; ?>">Preview</a>
                                                                    <?php } ?>

                                                                    <a data-id="<?= $fetch->id ?>" class="dropdown-item deletenote" data-doccategory="<?= $fetch->doc_type ?>" data-href="deletedocs" href="javascript:;">Delete</a>
                                                                    <a data-id="{{$fetch->id}}" class="dropdown-item backtodoc" data-doctype="{{$fetch->doc_type}}" data-href="backtodoc" href="javascript:;">Back To Document</a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php
                                                } //end foreach ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Preview Container -->
                                <div class="preview-pane file-preview-container preview-container-notuseddocumnetlist" style="display: inline;margin-top: 15px !important;width: 499px;">
                                    <h3>File Preview</h3>
                                    <p>Click on a file to preview it here.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
