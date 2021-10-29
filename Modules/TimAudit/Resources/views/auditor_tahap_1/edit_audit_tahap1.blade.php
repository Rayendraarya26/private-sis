@extends("layouts.layout_app")

@section('title', 'Proses Audit Tahap 1')

@push("css")
    <!-- HTML -->
    <link rel="stylesheet" href="{{asset("assets/plugins/smartwizard/css/smart_wizard_all.min.css")}}">
    <style>
        .step1_image {
            width: 100%;
            max-width: 400px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
@endpush

@section('content')
    <div class="dt-content">
        <div class="row">
			<a class="btn btn-sm btn-default" href="{{url("$url")}}" style="margin-bottom: 20px"><i class="fad fa-arrow-left"></i> Kembali</a>
            <div class="col-md-12">
                <div class="dt-card">
                    <div class="dt-card__body">
                        <!-- SmartWizard html -->
                        <div id="smartwizard">

                            <ul class="nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="#step-1">
                                        <strong>Langkah 1</strong> <br>Hasil Tinjauan Dokumen
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#step-2">
                                        <strong>Langkah 2</strong> <br>Laporan Audit Tahap 1
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div id="step-1" class="tab-pane" role="tabpanel" aria-labelledby="step-1">
                                    @include("timaudit::auditor_tahap_1._edit_audit_tahap1_step_1")
                                </div>
                                <div id="step-2" class="tab-pane" role="tabpanel" aria-labelledby="step-2">
                                    @include("timaudit::auditor_tahap_1._edit_audit_tahap1_step_2")
                                </div>
                            </div>
                        </div>

                        <br/> &nbsp;


                        <div class="row">
                            <div class="col-md-12">
                                <div style="float: right">
                                    <button class="btn btn-secondary" id="prev-btn" type="button">
                                        <i class="far fa-arrow-left-from-line"></i> Kembali
                                    </button>
                                    <button class="btn btn-secondary" id="next-btn" type="button">
                                        Lanjut <i class="far fa-arrow-right-from-line"></i>
                                    </button>
                                    {{--<button class="btn btn-danger" id="reset-btn" type="button">
                                        <i class="far fa-arrow-left-rotate"></i>
                                        Reset
                                    </button>--}}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push("javascript")
    <script src="{{asset("assets/plugins/smartwizard/js/jquery.smartWizard.min.js")}}"></script>
	<script src="https://cdn.tiny.cloud/1/hb65btdze8ubxfoabqu7fqjpuzpmx0c4k0je5f883m4l9ajf/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
		
		
        const swalWithBootstrapButtons = swal.mixin({
            confirmButtonClass: 'btn btn-primary mb-2',
            cancelButtonClass: 'btn btn-warning mr-2 mb-2',
            buttonsStyling: false,
        });

        $(document).ready(function () {
			@if($statusEntry == false)
				swalWithBootstrapButtons({
					title: `Informasi Audit Tahap 1`,
					text: `Data LKS dan Laporan belum pernah ter-entry, apakah anda ingin men-generate data dari master?`,
					type: 'info',
					showCancelButton: true,
					allowOutsideClick: false,
					confirmButtonText: 'Generate',
					cancelButtonText: 'Batal',
					reverseButtons: true
				}).then(async (result) => {
					if (result.value) {
						$.messager.progress();
						let formData = new FormData();
						formData.append("tipe", 'update-generate-tahap1')
						formData.append("jadw_audit_id", '{{$dataJadwal->jadw_audit_id}}')
						formData.append("sert_id", '{{$dataJadwal->sert_id}}')
						formData.append("mohon_id", '{{$dataJadwal->mohon_id}}')
						$.ajax({
							url: `{{action("$module@update")}}`,
							type: 'post',
							processData: false,
							contentType: false,
							data: formData,
							success: async function (res) {
								$.messager.progress('close');
								toastCenter({
									type: 'success',
									title: res.message
								})
								setTimeout(() => location.href = "{{url("$url")}}/edit?tipe=audit-tahap1&jadw_audit_id={{$dataJadwal->jadw_audit_id}}", 1000)
							},
							error: function (xhr) {
								$.messager.progress('close');
								self.loading_submit = false;
								if (xhr.readyState === 0) toastCenter({type: 'error', title: "Network Error"})
								else toastCenter({type: 'error', 'title': xhr.responseJSON.message})
							}
						});
					}
					else{
						setTimeout(() => location.href = "{{url("$url")}}", 500);
					}
				});
			@endif
            // ============================================ SmartWizard ============================================
            $("#smartwizard").on("showStep", function (e, anchorObject, stepNumber, stepDirection, stepPosition) {
                $("#prev-btn").removeClass('disabled');
                $("#next-btn").removeClass('disabled');
                if (stepPosition === 'first') {
                    $("#prev-btn").addClass('disabled');
                } else if (stepPosition === 'last') {
                    $("#next-btn").addClass('disabled');
                } else {
                    $("#prev-btn").removeClass('disabled');
                    $("#next-btn").removeClass('disabled');
                }
            });

            // Smart Wizard
            $('#smartwizard').smartWizard({
                selected: 0,
                cycleSteps: false,
                theme: 'arrows', // default, arrows, dots, progress
                enableURLhash: false,
                // darkMode: true,
                transition: {
                    animation: 'slide-horizontal', // Effect on navigation, none/fade/slide-horizontal/slide-vertical/slide-swing
                },
                toolbarSettings: {
                    toolbarPosition: 'bottom', // none, top, bottom, both
                    toolbarButtonPosition: 'right', // left, right, center
                    showNextButton: false, // show/hide a Next button
                    showPreviousButton: false, // show/hide a Previous button
                    toolbarExtraButtons: [] // Extra buttons to show on toolbar, array of jQuery input/buttons elements
                },
                anchorSettings: {
                    anchorClickable: false, // Enable/Disable anchor navigation
                    removeDoneStepOnNavigateBack: true, // While navigate back done step after active step will be cleared
                },
                keyboardSettings: {
                    keyNavigation: false,
                },
            });

            $("#prev-btn").on("click", function () {
                // Navigate previous
                $('#smartwizard').smartWizard("prev");
                return true;
            });

            $("#next-btn").on("click", function () {
                try {
                    const currentStep = $('#smartwizard').smartWizard("getStepIndex");
                    // Validate STEP
                    switch (currentStep) {
                        case 0:
                            vueStepOne.validate();
                            vueStepTwo.start();
							
                            break;
                    }

                    // Navigate next
                    $('#smartwizard').smartWizard("next");
                    return true;
                } catch (message) {
                    swalWithBootstrapButtons({
                        title: `Validasi`,
                        text: message,
                        type: 'warning',
                    })
                }
            });

        });
    </script>
@endpush
