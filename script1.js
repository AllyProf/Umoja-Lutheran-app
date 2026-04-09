      function markNotificationAsRead(event, notificationId, url) {
        if (event) event.preventDefault();
        
        // If we have a URL, navigate to it immediately (optimistic navigation)
        // We trigger the validation in background
        const hasUrl = url && url !== 'javascript:;' && url !== '#';
        
        fetch(`/notifications/${notificationId}/read`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        }).catch(function(err) { console.error('Error marking read:', err); });
        
        if (hasUrl) {
            window.location.href = url;
            return false;
        }
        
        // DOM update logic (only relevant if we stay on page)
        // ... (preserving existing logic for single page app feel if no URL)

          // Update badge count
          const badge = document.querySelector('.notification-badge');
          if (badge) {
            const currentText = badge.textContent.trim();
            const count = parseInt(currentText.replace('+', '')) - 1;
            if (count > 0) {
              badge.textContent = count > 99 ? '99+' : count;
            } else {
              badge.remove();
            }
          }
          // Remove the notification from the dropdown
          const notificationItem = document.querySelector(`a[onclick*="${notificationId}"]`)?.closest('li');
          if (notificationItem) {
            notificationItem.remove();
          }
          // Update notification title
          updateNotificationTitle();
          // Dismiss toast if it exists
          dismissToast(notificationId);
      }
      
      function updateNotificationTitle() {
        const badge = document.querySelector('.notification-badge');
        const title = document.querySelector('.app-notification__title');
        if (title) {
          const count = badge ? parseInt(badge.textContent.trim().replace('+', '')) : 0;
          if (count > 0) {
            title.innerHTML = `You have ${count} new ${count === 1 ? 'notification' : 'notifications'}.`;
          } else {
            title.innerHTML = 'You have no new notifications.';
          }
        }
      }
      
      // Ensure notification badge is visible on page load
      document.addEventListener('DOMContentLoaded', function() {
        const badge = document.querySelector('.notification-badge');
        const unreadCount = 6;
        if (unreadCount > 0 && !badge) {
          // Badge should exist but doesn't - recreate it
          const notificationLink = document.querySelector('.app-nav__item[data-toggle="dropdown"]');
          if (notificationLink) {
            const newBadge = document.createElement('span');
            newBadge.className = 'badge badge-danger notification-badge';
            newBadge.style.cssText = 'position: absolute; top: -5px; right: -8px; font-size: 10px; font-weight: bold; padding: 2px 6px; border-radius: 10px; min-width: 18px; height: 18px; text-align: center; line-height: 14px; z-index: 1000; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.2);';
            newBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
            notificationLink.appendChild(newBadge);
          }
        }
      });

      function markAllNotificationsAsRead() {
        fetch('/notifications/mark-all-read', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        }).then(() => {
          location.reload();
        });
      }

      function incrementNotificationBadge(count = 1) {
        const badge = document.querySelector('.notification-badge');
        if (badge) {
          const currentCount = parseInt(badge.textContent.trim().replace('+', '')) || 0;
          const newCount = currentCount + count;
          badge.textContent = newCount > 99 ? '99+' : newCount;
          updateNotificationTitle();
        } else {
          const notificationLink = document.querySelector('.app-nav__item[data-toggle="dropdown"]');
          if (notificationLink) {
            const newBadge = document.createElement('span');
            newBadge.className = 'badge badge-danger notification-badge';
            newBadge.style.cssText = 'position: absolute; top: -5px; right: -8px; font-size: 10px; font-weight: bold; padding: 2px 6px; border-radius: 10px; min-width: 18px; height: 18px; text-align: center; line-height: 14px; z-index: 1000; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.2);';
            newBadge.textContent = count > 99 ? '99+' : count;
            notificationLink.appendChild(newBadge);
            updateNotificationTitle();
          }
        }
      }
      }

      // Toast Notification System for Action Required Notifications
      // Load previously shown notification IDs from localStorage
      const storedShownIds = localStorage.getItem('shownToastNotificationIds');
      const shownToastIds = new Set(storedShownIds ? JSON.parse(storedShownIds) : []);
      
      // Function to save shown IDs to localStorage
      function saveShownToastIds() {
        localStorage.setItem('shownToastNotificationIds', JSON.stringify(Array.from(shownToastIds)));
      }
      
      // Notification queue system - show one at a time
      let notificationQueue = [];
      let currentNotificationTimer = null;
      let isShowingNotification = false;
      
      function showToastNotification(notification) {
        // Don't show if already shown or if notification is read
        if (shownToastIds.has(notification.id) || notification.is_read) {
          return;
        }
        
        // Only show notifications that require action (not informational ones)
        // The backend already filters to only actionable notifications, but we double-check here
        const userRole = 'bar_keeper';
        let shouldShow = false;
        
        // Reception: see almost everything actionable
        if (userRole === 'reception') {
          shouldShow = notification.type === 'service_request' || 
                       notification.type === 'booking' || 
                       notification.type === 'extension_request' ||
                       notification.type === 'issue_report' ||
                       notification.type === 'maintenance';
        } 
        // Manager/Super Admin: see EVERYTHING actionable
        else if (userRole === 'manager' || userRole === 'super_admin') {
          shouldShow = true; // Managers get all alerts
        }
        // Bar Keeper: show relevant alerts
        else if (userRole === 'bar_keeper') {
          shouldShow = notification.type === 'booking' || 
                       notification.type === 'service_request' ||
                       notification.type === 'payment';
        }
        // Head Chef: show relevant alerts
        else if (userRole === 'head_chef') {
          shouldShow = notification.type === 'booking' || 
                       notification.type === 'service_request';
        }
        // Customer: show toast notifications for status updates
        else if (userRole === 'customer') {
          shouldShow = true;
        }
        
        if (!shouldShow) {
          return;
        }
        
        // Add to queue if not already there
        if (!notificationQueue.find(n => n.id === notification.id)) {
          notificationQueue.push(notification);
        }
        
        // Start showing if not already showing
        if (!isShowingNotification) {
          showNextNotification();
        }
      }
      
      function showNextNotification() {
        // Check if there are more notifications
        if (notificationQueue.length === 0) {
          isShowingNotification = false;
          return;
        }
        
        isShowingNotification = true;
        const notification = notificationQueue.shift(); // Get and remove first notification
        
        // Don't show if already shown
        if (shownToastIds.has(notification.id)) {
          showNextNotification(); // Skip to next
          return;
        }
        
        shownToastIds.add(notification.id);
        saveShownToastIds(); // Save to localStorage
        
        // Use the professional Toast system
        showToast(
          notification.color || 'info', 
          notification.message, 
          notification.title || 'New Notification',
          5000 // Actionable notifications stay longer
        );

        // Schedule next notification in the queue
        setTimeout(showNextNotification, 6000);
      }
      
      function dismissCurrentNotification() {
        const container = document.getElementById('toast-container');
        const toast = container.querySelector('.toast-notification');
        
        if (toast) {
          toast.classList.add('removing');
          setTimeout(function() {
            toast.remove();
            // Show next notification
            showNextNotification();
          }, 300);
        } else {
          showNextNotification();
        }
      }
      
      function dismissToast(notificationId) {
        // Remove from queue if present
        notificationQueue = notificationQueue.filter(n => n.id !== notificationId);
        
        const toast = document.getElementById(`toast-${notificationId}`);
        if (toast) {
          dismissCurrentNotification();
        }
      }
      
      // Show actionable notifications after 2 seconds (one at a time, rotating every 5 seconds)
      // Only unread actionable notifications will be shown (already filtered by backend)
                      setTimeout(function() {
          const notifications = [{"id":204,"type":"booking","title":"Special Request from Guest","message":"Guest JOAN KOMBE (Room 1B) has special requests: no smocking","color":"info","link":"https:\/\/umoja-lutheran.mauzolink.co.tz\/bar-keeper\/dashboard","is_read":false},{"id":197,"type":"booking","title":"Special Request from Guest","message":"Guest ben mongi (Room 4B) has special requests: no smoke","color":"info","link":"https:\/\/umoja-lutheran.mauzolink.co.tz\/bar-keeper\/dashboard","is_read":false},{"id":169,"type":"booking","title":"Special Request from Guest","message":"Guest BENJA (Room 201) has special requests: lkjhgf","color":"info","link":"https:\/\/umoja-lutheran.mauzolink.co.tz\/bar-keeper\/dashboard","is_read":false},{"id":158,"type":"booking","title":"Special Request from Guest","message":"Guest ngaz (Room 203) has special requests: ;kjhgfds","color":"info","link":"https:\/\/umoja-lutheran.mauzolink.co.tz\/bar-keeper\/dashboard","is_read":false},{"id":155,"type":"booking","title":"Special Request from Guest","message":"Guest ver viva (Room 113) has special requests: ;ljh","color":"info","link":"https:\/\/umoja-lutheran.mauzolink.co.tz\/bar-keeper\/dashboard","is_read":false},{"id":150,"type":"booking","title":"Special Request from Guest","message":"Guest chasambi wilson (Room 112) has special requests: \u0027poiuy","color":"info","link":"https:\/\/umoja-lutheran.mauzolink.co.tz\/bar-keeper\/dashboard","is_read":false}];
          
          // Add all unread actionable notifications to queue (they're already filtered by backend)
          notifications.forEach(function(notification) {
            if (!notification.is_read) {
              showToastNotification(notification);
            }
          });
        }, 2000);
            
      // Poll for new actionable notifications every 30 seconds
      setInterval(function() {
        fetch('/notifications/actionable', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
          }
        })
        .then(function(response) {
          const contentType = response.headers.get('content-type');
          if (contentType && contentType.includes('application/json')) {
            return response.json();
          } else {
            // If not JSON, return empty notifications
            return { success: false, notifications: [] };
          }
        })
        .then(function(data) {
          if (data.success) {
            if (data.notifications && data.notifications.length > 0) {
              data.notifications.forEach(function(notification) {
                if (!shownToastIds.has(notification.id) && !notification.is_read) {
                  // Add to queue instead of showing immediately
                  showToastNotification(notification);
                }
              });
            }
            
            // Update badge count using the unread_count from response (always if success)
            if (typeof data.unread_count !== 'undefined') {
              const unreadCount = data.unread_count;
              const badge = document.querySelector('.notification-badge');
              
              if (unreadCount > 0) {
                if (badge) {
                  badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                } else {
                  // Create badge if it doesn't exist
                  const notificationLink = document.querySelector('.app-nav__item[data-toggle="dropdown"]');
                  if (notificationLink) {
                    const newBadge = document.createElement('span');
                    newBadge.className = 'badge badge-danger notification-badge';
                    newBadge.style.cssText = 'position: absolute; top: -5px; right: -8px; font-size: 10px; font-weight: bold; padding: 2px 6px; border-radius: 10px; min-width: 18px; height: 18px; text-align: center; line-height: 14px; z-index: 1000; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.2);';
                    newBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                    notificationLink.appendChild(newBadge);
                  }
                }
                updateNotificationTitle();
              } else if (badge) {
                badge.remove();
                updateNotificationTitle();
              }
            }
          }
        })
        .catch(function(error) {
          // Silently fail - don't spam console with notification errors
          if (error.message && !error.message.includes('JSON')) {
            console.error('Error fetching actionable notifications:', error);
          }
        });
      }, 15000);
    </script>