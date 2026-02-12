// Booking Date Picker Initialization
(function() {
    'use strict';
    
    function initBookingDatePickers() {
        // Wait for Flatpickr to load
        if (typeof flatpickr === 'undefined') {
            setTimeout(initBookingDatePickers, 100);
            return;
        }
        
        // Get input elements
        var checkInInput = document.getElementById('check_in');
        var checkOutInput = document.getElementById('check_out');
        
        if (!checkInInput || !checkOutInput) {
            setTimeout(initBookingDatePickers, 100);
            return;
        }
        
        // Get today's date
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        
        // Initialize check-in picker
        window.checkInPickerInstance = flatpickr(checkInInput, {
            enableTime: false,
            dateFormat: 'Y-m-d',
            minDate: today,
            clickOpens: true,
            disableMobile: false,
            allowInput: false,
            wrap: false,
            static: false,
            appendTo: document.body,
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0 && window.checkOutPickerInstance) {
                    var minCheckOut = new Date(selectedDates[0]);
                    minCheckOut.setDate(minCheckOut.getDate() + 1);
                    minCheckOut.setHours(0, 0, 0, 0);
                    window.checkOutPickerInstance.set('minDate', minCheckOut);
                    if (window.checkOutPickerInstance.selectedDates.length > 0) {
                        if (window.checkOutPickerInstance.selectedDates[0] <= selectedDates[0]) {
                            window.checkOutPickerInstance.clear();
                        }
                    }
                }
            }
        });
        
        // Initialize check-out picker
        var minCheckOutDate = new Date(today);
        minCheckOutDate.setDate(minCheckOutDate.getDate() + 1);
        window.checkOutPickerInstance = flatpickr(checkOutInput, {
            enableTime: false,
            dateFormat: 'Y-m-d',
            minDate: minCheckOutDate,
            clickOpens: true,
            disableMobile: false,
            allowInput: false,
            wrap: false,
            static: false,
            appendTo: document.body
        });
        
        // Add click handlers to inputs
        checkInInput.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (window.checkInPickerInstance) {
                window.checkInPickerInstance.open();
            }
        });
        
        checkOutInput.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (window.checkOutPickerInstance) {
                window.checkOutPickerInstance.open();
            }
        });
        
        // Add click handlers to calendar icons
        var checkInGroup = checkInInput.closest('.input-group');
        if (checkInGroup) {
            var checkInIcon = checkInGroup.querySelector('.input-group-addon');
            if (checkInIcon) {
                checkInIcon.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (window.checkInPickerInstance) {
                        window.checkInPickerInstance.open();
                    }
                });
            }
        }
        
        var checkOutGroup = checkOutInput.closest('.input-group');
        if (checkOutGroup) {
            var checkOutIcon = checkOutGroup.querySelector('.input-group-addon');
            if (checkOutIcon) {
                checkOutIcon.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (window.checkOutPickerInstance) {
                        window.checkOutPickerInstance.open();
                    }
                });
            }
        }
    }
    
    // Initialize when ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBookingDatePickers);
    } else {
        setTimeout(initBookingDatePickers, 100);
    }
})();

