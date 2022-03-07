@extends("layouts.layout_app")

@section('title', 'Detail Sertifikasi')

@section('content')
    <div class="dt-content">

        <div class="row">
            <div class="col-xl-12">
                <!-- Card -->
                <div class="dt-card dt-card__full-height">
                    <!-- Card Body -->
                    <div class="dt-card__body">
                        <div id="smartwizard">
                            <ul class="nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="#step-1">
                                        <div style="padding: 5px 0 5px 0">
                                            <strong style="font-size: 16px;">Data Dokumen</strong>
                                        </div>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#step-2">
                                        <div style="padding: 5px 0 5px 0">
                                            <strong style="font-size: 16px;">Data Perusahaan</strong>
                                        </div>
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div id="step-1" class="tab-pane" role="tabpanel" aria-labelledby="step-1">
                                    @include("$view._detail_tab_1")
                                </div>
                                <div id="step-2" class="tab-pane" role="tabpanel" aria-labelledby="step-2">
                                    @include("$view._detail_tab_2")
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{asset("assets/plugins/smartwizard/css/smart_wizard_all.min.css")}}">
@endpush

@push('javascript')
    <script src="{{asset("assets/plugins/smartwizard/js/jquery.smartWizard.min.js")}}"></script>
    <script>
        $(document).ready(function () {
            // ============================================ SmartWizard ============================================
            $('#smartwizard').smartWizard({
                selected: 0,
                cycleSteps: true,
                theme: 'default', // default, arrows, dots, progress
                enableURLhash: true,
                enableAllAnchors: true,
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
                    anchorClickable: true, // Enable/Disable anchor navigation
                    removeDoneStepOnNavigateBack: false, // While navigate back done step after active step will be cleared
                    enableAllAnchors: true, // Activates all anchors clickable all times
                    markDoneStep: false, // add done css
                    enableAnchorOnDoneStep: true // Enable/Disable the done steps navigation
                },
                keyboardSettings: {
                    keyNavigation: true,
                },
            });

            $("#prev-btn").on("click", function () {
                // Navigate previous
                $('#smartwizard').smartWizard("prev");
                return true;
            });
        });
    </script>
@endpush
