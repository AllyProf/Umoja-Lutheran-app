// Simple date picker - exactly like test page
(function() {
    function initDatePickers() {
        if (typeof flatpickr === 'undefined') {
            setTimeout(initDatePickers, 100);
            return;
        }
        
        var checkInInput = document.getElementById('check_in');
        var checkOutInput = document.getElementById('check_out');
        
        if (!checkInInput || !checkOutInput) {
            setTimeout(initDatePickers, 100);
            return;
        }
        
        var checkInPicker = flatpickr(checkInInput, {
            enableTime: false,
            dateFormat: 'Y-m-d',
            minDate: 'today',
            clickOpens: true
        });
        
        var checkOutPicker = flatpickr(checkOutInput, {
            enableTime: false,
            dateFormat: 'Y-m-d',
            minDate: 'today',
            clickOpens: true
        });
        
        checkInInput.addEventListener('click', function() {
            checkInPicker.open();
        });
        
        checkOutInput.addEventListener('click', function() {
            checkOutPicker.open();
        });
        
        // Also handle icon clicks
        var checkInGroup = checkInInput.closest('.input-group');
        if (checkInGroup) {
            var checkInIcon = checkInGroup.querySelector('.input-group-addon');
            if (checkInIcon) {
                checkInIcon.addEventListener('click', function() {
                    checkInPicker.open();
                });
            }
        }
        
        var checkOutGroup = checkOutInput.closest('.input-group');
        if (checkOutGroup) {
            var checkOutIcon = checkOutGroup.querySelector('.input-group-addon');
            if (checkOutIcon) {
                checkOutIcon.addEventListener('click', function() {
                    checkOutPicker.open();
                });
            }
        }
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDatePickers);
    } else {
        initDatePickers();
    }
})();











