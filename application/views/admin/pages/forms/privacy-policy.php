<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <!-- Main content -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h4>Privacy Policy And Terms & Conditions</h4>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a class="text text-info"
                href="<?= base_url('admin/home') ?>"><?= display_breadcrumbs(); ?></a></li>
            <!-- <li class="breadcrumb-item active">Orders</li> -->
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <div class="card card-info">
            <!-- form start -->
            <form class="form-horizontal form-submit-event" action="<?= base_url('admin/Privacy_policy/update-privacy-policy-settings'); ?>" method="POST" enctype="multipart/form-data">
              <div class="card-body pad">
                <label for="privacy_policy_content"> Privacy Policy </label>
                <a href="<?= base_url('admin/privacy-policy/privacy-policy-page') ?>" target='_blank' class="btn btn-primary btn-xs" title='View Privacy Policy'><i class='fa fa-eye'></i></a>
                <div class="mb-3">
                  <!-- Language Tabs for Privacy Policy -->
                  <ul class="nav nav-tabs" id="privacyPolicyTabs" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="privacy-policy-en-tab" data-toggle="tab" href="#privacy-policy-en" role="tab">English</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="privacy-policy-ar-tab" data-toggle="tab" href="#privacy-policy-ar" role="tab">Arabic</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="privacy-policy-he-tab" data-toggle="tab" href="#privacy-policy-he" role="tab">Hebrew</a>
                    </li>
                  </ul>
                  <div class="tab-content mt-2" id="privacyPolicyTabContent">
                    <div class="tab-pane fade show active" id="privacy-policy-en" role="tabpanel">
                      <textarea name="privacy_policy_input_description" id="privacy_policy_input_description" class="textarea text_editor" placeholder="Place some text here (English)">
                          <?= isset($privacy_policy) ? $privacy_policy : '' ?>
                      </textarea>
                      <input type="hidden" id="privacy_policy_input_description_en" name="privacy_policy_translations[en][value]" value="<?= isset($privacy_policy_translations['en']['value']) ? htmlspecialchars($privacy_policy_translations['en']['value']) : (isset($privacy_policy) ? htmlspecialchars($privacy_policy) : '') ?>">
                    </div>
                    <div class="tab-pane fade" id="privacy-policy-ar" role="tabpanel">
                      <textarea name="privacy_policy_translations[ar][value]" id="privacy_policy_input_description_ar" class="textarea text_editor" dir="rtl" placeholder="ضع النص هنا (Arabic)">
                          <?= isset($privacy_policy_translations['ar']['value']) ? $privacy_policy_translations['ar']['value'] : '' ?>
                      </textarea>
                    </div>
                    <div class="tab-pane fade" id="privacy-policy-he" role="tabpanel">
                      <textarea name="privacy_policy_translations[he][value]" id="privacy_policy_input_description_he" class="textarea text_editor" dir="rtl" placeholder="הכנס טקסט כאן (Hebrew)">
                          <?= isset($privacy_policy_translations['he']['value']) ? $privacy_policy_translations['he']['value'] : '' ?>
                      </textarea>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-body pad">
                <label for="terms_conditions_content">Terms & Conditions </label>
                <a href="<?= base_url('admin/privacy-policy/terms-and-conditions-page') ?>" target='_blank' class="btn btn-primary btn-xs" title='View Terms && Condition'><i class='fa fa-eye'></i></a>
                <div class="mb-3">
                  <!-- Language Tabs for Terms & Conditions -->
                  <ul class="nav nav-tabs" id="termsConditionsTabs" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="terms-conditions-en-tab" data-toggle="tab" href="#terms-conditions-en" role="tab">English</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="terms-conditions-ar-tab" data-toggle="tab" href="#terms-conditions-ar" role="tab">Arabic</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="terms-conditions-he-tab" data-toggle="tab" href="#terms-conditions-he" role="tab">Hebrew</a>
                    </li>
                  </ul>
                  <div class="tab-content mt-2" id="termsConditionsTabContent">
                    <div class="tab-pane fade show active" id="terms-conditions-en" role="tabpanel">
                      <textarea name="terms_n_conditions_input_description" id="terms_n_conditions_input_description" class="textarea text_editor" placeholder="Place some text here (English)">
                          <?= isset($terms_n_condition) ? $terms_n_condition : '' ?>
                      </textarea>
                      <input type="hidden" id="terms_n_conditions_input_description_en" name="terms_n_conditions_translations[en][value]" value="<?= isset($terms_n_conditions_translations['en']['value']) ? htmlspecialchars($terms_n_conditions_translations['en']['value']) : (isset($terms_n_condition) ? htmlspecialchars($terms_n_condition) : '') ?>">
                    </div>
                    <div class="tab-pane fade" id="terms-conditions-ar" role="tabpanel">
                      <textarea name="terms_n_conditions_translations[ar][value]" id="terms_n_conditions_input_description_ar" class="textarea text_editor" dir="rtl" placeholder="ضع النص هنا (Arabic)">
                          <?= isset($terms_n_conditions_translations['ar']['value']) ? $terms_n_conditions_translations['ar']['value'] : '' ?>
                      </textarea>
                    </div>
                    <div class="tab-pane fade" id="terms-conditions-he" role="tabpanel">
                      <textarea name="terms_n_conditions_translations[he][value]" id="terms_n_conditions_input_description_he" class="textarea text_editor" dir="rtl" placeholder="הכנס טקסט כאן (Hebrew)">
                          <?= isset($terms_n_conditions_translations['he']['value']) ? $terms_n_conditions_translations['he']['value'] : '' ?>
                      </textarea>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <button type="reset" class="btn btn-warning">Reset</button>
                  <button type="submit" class="btn btn-info" id="submit_btn">Update Privacy Policy And Terms & Conditions</button>
                </div>
              </div>

              <!-- /.card-body -->
            </form>
          </div>
          <!--/.card-->
        </div>
        <!--/.col-md-12-->
      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>