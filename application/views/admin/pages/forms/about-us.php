<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <!-- Main content -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h4>About Us</h4>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a class="text text-info"
                href="<?= base_url('admin/home') ?>"><?= display_breadcrumbs(); ?></a></li>
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
            <form class="form-horizontal form-submit-event" action="<?= base_url('admin/About_us/update-about-us-settings'); ?>" method="POST" enctype="multipart/form-data">
              <div class="card-body pad">
                <label for="about_us_content"> About Us </label>
                <div class="mb-3">
                  <!-- Language Tabs for About Us -->
                  <ul class="nav nav-tabs" id="aboutUsTabs" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="about-us-en-tab" data-toggle="tab" href="#about-us-en" role="tab">English</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="about-us-ar-tab" data-toggle="tab" href="#about-us-ar" role="tab">Arabic</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="about-us-he-tab" data-toggle="tab" href="#about-us-he" role="tab">Hebrew</a>
                    </li>
                  </ul>
                  <div class="tab-content mt-2" id="aboutUsTabContent">
                    <div class="tab-pane fade show active" id="about-us-en" role="tabpanel">
                      <textarea name="about_us_input_description" id="about_us_input_description" class="textarea text_editor" placeholder="Place some text here (English)">
                          <?= isset($about_us) ? $about_us : '' ?>
                      </textarea>
                      <input type="hidden" id="about_us_input_description_en" name="setting_translations[en][value]" value="<?= isset($setting_translations['en']['value']) ? htmlspecialchars($setting_translations['en']['value']) : (isset($about_us) ? htmlspecialchars($about_us) : '') ?>">
                    </div>
                    <div class="tab-pane fade" id="about-us-ar" role="tabpanel">
                      <textarea name="setting_translations[ar][value]" id="about_us_input_description_ar" class="textarea text_editor" dir="rtl" placeholder="ضع النص هنا (Arabic)">
                          <?= isset($setting_translations['ar']['value']) ? $setting_translations['ar']['value'] : '' ?>
                      </textarea>
                    </div>
                    <div class="tab-pane fade" id="about-us-he" role="tabpanel">
                      <textarea name="setting_translations[he][value]" id="about_us_input_description_he" class="textarea text_editor" dir="rtl" placeholder="הכנס טקסט כאן (Hebrew)">
                          <?= isset($setting_translations['he']['value']) ? $setting_translations['he']['value'] : '' ?>
                      </textarea>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <button type="reset" class="btn btn-warning">Reset</button>
                  <button type="submit" class="btn btn-info" id="submit_btn">Update About Us</button>
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