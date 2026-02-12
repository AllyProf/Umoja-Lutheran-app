<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="{{ asset('royal-master/js/jquery-3.2.1.min.js') }}"></script>
<script src="{{ asset('royal-master/js/popper.js') }}"></script>
<script src="{{ asset('royal-master/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('royal-master/vendors/owl-carousel/owl.carousel.min.js') }}"></script>

<script src="{{ asset('royal-master/js/mail-script.js') }}"></script>
<script src="{{ asset('royal-master/vendors/nice-select/js/jquery.nice-select.js') }}"></script>
<!-- Flatpickr - Advanced Date Picker -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="{{ asset('royal-master/js/stellar.js') }}"></script>
<script src="{{ asset('royal-master/vendors/lightbox/simpleLightbox.min.js') }}"></script>
<script src="{{ asset('royal-master/js/custom.js') }}"></script>

<!-- Ensure carousels initialize properly -->
<script>
$(document).ready(function() {
    // Wait for images to load before initializing carousels
    $(window).on('load', function() {
        initializeCarousels();
    });
    
    // Also try initializing immediately in case window load already fired
    setTimeout(function() {
        initializeCarousels();
    }, 100);
});

function initializeCarousels() {
    // Initialize testimonial slider if not already initialized
    $('.testimonial_slider').each(function() {
        if (!$(this).hasClass('owl-loaded')) {
            $(this).owlCarousel({
                loop: true,
                margin: 30,
                items: 3,
                nav: false,
                autoplay: true,
                dots: true,
                smartSpeed: 1500,
                responsiveClass: true,
                responsive: {
                    0: {
                        items: 1,
                    },
                    768: {
                        items: 2,
                    },
                    992: {
                        items: 3,
                    },
                }
            });
        }
    });
    
    // Initialize gallery slider if not already initialized
    $('.gallery_slider').each(function() {
        if (!$(this).hasClass('owl-loaded')) {
            $(this).owlCarousel({
                loop: true,
                margin: 0,
                items: 4,
                nav: false,
                autoplay: true,
                autoplaySpeed: 2000,
                dots: false,
                smartSpeed: 1000,
                responsiveClass: true,
                responsive: {
                    0: {
                        items: 1,
                    },
                    576: {
                        items: 2,
                    },
                    768: {
                        items: 3,
                    },
                    992: {
                        items: 4,
                    },
                }
            });
        }
    });
}
</script>

<!-- Advanced Date Picker Initialization for Homepage -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get today's date
    var today = new Date();
    today.setHours(0, 0, 0, 0);
    
    // Initialize check-in datepicker
    var homeCheckInPicker = flatpickr('#home_check_in', {
        enableTime: false,
        dateFormat: 'Y-m-d',
        minDate: today,
        onChange: function(selectedDates, dateStr, instance) {
            // When check-in date changes, update check-out min date
            if (selectedDates.length > 0) {
                var minCheckOut = new Date(selectedDates[0]);
                minCheckOut.setDate(minCheckOut.getDate() + 1);
                minCheckOut.setHours(0, 0, 0, 0);
                
                // Update check-out picker
                homeCheckOutPicker.set('minDate', minCheckOut);
                
                // Clear check-out if it's invalid
                if (homeCheckOutPicker.selectedDates.length > 0) {
                    if (homeCheckOutPicker.selectedDates[0] <= selectedDates[0]) {
                        homeCheckOutPicker.clear();
                    }
                }
            }
        }
    });
    
    // Make calendar icon clickable for check-in
    var checkInIcon = document.querySelector('#home_check_in').parentElement.querySelector('.input-group-addon');
    if (checkInIcon) {
        checkInIcon.addEventListener('click', function() {
            homeCheckInPicker.open();
        });
    }
    
    // Initialize check-out datepicker
    var homeCheckOutPicker = flatpickr('#home_check_out', {
        enableTime: false,
        dateFormat: 'Y-m-d',
        minDate: (function() {
            var min = new Date(today);
            min.setDate(min.getDate() + 1);
            return min;
        })()
    });
    
    // Make calendar icon clickable for check-out
    var checkOutIcon = document.querySelector('#home_check_out').parentElement.querySelector('.input-group-addon');
    if (checkOutIcon) {
        checkOutIcon.addEventListener('click', function() {
            homeCheckOutPicker.open();
        });
    }
});
</script>

<style>
/* Advanced Flatpickr Date Picker Styling for Homepage */
.flatpickr-calendar {
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    border: 1px solid #e0e0e0;
    font-family: inherit;
}

.flatpickr-months {
    background: linear-gradient(135deg, #e77a3a 0%, #d66a2a 100%) !important;
    border-radius: 10px 10px 0 0;
    padding: 10px 0;
    position: relative;
    z-index: 1;
}

.flatpickr-month {
    color: white !important;
    background: transparent !important;
}

.flatpickr-prev-month,
.flatpickr-next-month {
    color: white !important;
    fill: white !important;
    background: transparent !important;
}

.flatpickr-prev-month:hover,
.flatpickr-next-month:hover {
    background: rgba(255, 255, 255, 0.2) !important;
    border-radius: 5px;
}

.flatpickr-current-month {
    color: white !important;
    font-weight: 600;
    background: transparent !important;
}

.flatpickr-current-month .flatpickr-monthDropdown-months {
    background: transparent !important;
    color: white !important;
    font-weight: 600;
}

.flatpickr-current-month .flatpickr-monthDropdown-months option {
    background: #e77a3a !important;
    color: white !important;
}

/* Ensure month and year text are visible */
.flatpickr-current-month input.cur-year,
.flatpickr-current-month .flatpickr-monthDropdown-months {
    color: white !important;
    background: transparent !important;
    font-weight: 600 !important;
}

.flatpickr-current-month input.cur-year {
    color: white !important;
    background: transparent !important;
}

/* Fix for month dropdown */
.flatpickr-monthDropdown-months {
    background: transparent !important;
    color: white !important;
    border: none !important;
}

.flatpickr-weekdays {
    background: #f8f9fa;
    padding: 10px 0;
}

.flatpickr-weekday {
    color: #000000; /* Brand color: Black */
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
}

.flatpickr-day {
    border-radius: 5px;
    transition: all 0.2s ease;
    color: #000000; /* Brand color: Black */
}

.flatpickr-day:hover {
    background: #e77a3a; /* Brand color: Orange */
    color: white;
    border-color: #e77a3a;
}

.flatpickr-day.selected,
.flatpickr-day.startRange,
.flatpickr-day.endRange {
    background: #e77a3a; /* Brand color: Orange */
    border-color: #e77a3a;
    color: white;
    font-weight: 600;
}

.flatpickr-day.today {
    border-color: #e77a3a; /* Brand color: Orange */
    color: #000000; /* Brand color: Black */
    font-weight: 600;
}

.flatpickr-day.disabled,
.flatpickr-day.flatpickr-disabled {
    color: #ccc;
    cursor: not-allowed;
    background: #f5f5f5;
}

.flatpickr-day.disabled:hover {
    background: #f5f5f5;
    color: #ccc;
}

#home_check_in[readonly],
#home_check_out[readonly] {
    background-color: white;
    cursor: pointer;
}

#home_check_in[readonly]:focus,
#home_check_out[readonly]:focus {
    border-color: #e77a3a; /* Brand color: Orange */
    box-shadow: 0 0 0 0.2rem rgba(231, 122, 58, 0.25);
    color: #000000; /* Brand color: Black */
}

#home_check_in[readonly],
#home_check_out[readonly] {
    color: #000000; /* Brand color: Black */
}

/* Hide any time picker elements - DATE ONLY */
.flatpickr-time,
.flatpickr-time-wrapper,
.flatpickr-am-pm {
    display: none !important;
}
</style>

<script>
// Mobile Menu Improvements
$(document).ready(function() {
    // Close menu when clicking overlay
    $('.navbar-collapse').on('click', function(e) {
        if (e.target === this) {
            $('.navbar-toggler').click();
        }
    });
    
    // Close menu when clicking a nav link
    $('.navbar-nav .nav-link').on('click', function() {
        if (window.innerWidth < 768) {
            $('.navbar-collapse').collapse('hide');
        }
    });
    
    // Update aria-expanded on collapse events
    $('#navbarSupportedContent').on('show.bs.collapse', function() {
        $('.navbar-toggler').attr('aria-expanded', 'true');
    });
    
    $('#navbarSupportedContent').on('hide.bs.collapse', function() {
        $('.navbar-toggler').attr('aria-expanded', 'false');
    });
});
</script>
