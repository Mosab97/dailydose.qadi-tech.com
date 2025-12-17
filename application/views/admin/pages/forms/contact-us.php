<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <!-- Main content -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h4>Contact Us</h4>
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
            <form class="form-horizontal form-submit-event" action="<?= base_url('admin/Contact_us/update-contact-settings'); ?>" method="POST" enctype="multipart/form-data">
              <div class="card-body pad">
                <label for="contact_us_content">Contact Us </label>
                <div class="mb-3">
                  <!-- Language Tabs for Contact Us -->
                  <ul class="nav nav-tabs" id="contactUsTabs" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="contact-us-en-tab" data-toggle="tab" href="#contact-us-en" role="tab">English</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="contact-us-ar-tab" data-toggle="tab" href="#contact-us-ar" role="tab">Arabic</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="contact-us-he-tab" data-toggle="tab" href="#contact-us-he" role="tab">Hebrew</a>
                    </li>
                  </ul>
                  <div class="tab-content mt-2" id="contactUsTabContent">
                    <div class="tab-pane fade show active" id="contact-us-en" role="tabpanel">
                      <textarea name="contact_input_description" id="contact_input_description" class="textarea text_editor" placeholder="Place some text here (English)">
                          <?= isset($contact_info) ? $contact_info : '' ?>
                      </textarea>
                      <input type="hidden" id="contact_input_description_en" name="setting_translations[en][value]" value="<?= isset($setting_translations['en']['value']) ? htmlspecialchars($setting_translations['en']['value']) : (isset($contact_info) ? htmlspecialchars($contact_info) : '') ?>">
                    </div>
                    <div class="tab-pane fade" id="contact-us-ar" role="tabpanel">
                      <textarea name="setting_translations[ar][value]" id="contact_input_description_ar" class="textarea text_editor" dir="rtl" placeholder="ضع النص هنا (Arabic)">
                          <?= isset($setting_translations['ar']['value']) ? $setting_translations['ar']['value'] : '' ?>
                      </textarea>
                    </div>
                    <div class="tab-pane fade" id="contact-us-he" role="tabpanel">
                      <textarea name="setting_translations[he][value]" id="contact_input_description_he" class="textarea text_editor" dir="rtl" placeholder="הכנס טקסט כאן (Hebrew)">
                          <?= isset($setting_translations['he']['value']) ? $setting_translations['he']['value'] : '' ?>
                      </textarea>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <button type="reset" class="btn btn-warning">Reset</button>
                  <button type="submit" class="btn btn-info" id="submit_btn">Update Contact Info</button>
                </div>
              </div>
          </div>
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