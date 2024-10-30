// script.js
document.getElementById('notificationBtn').addEventListener('click', function() {
    var panel = document.getElementById('notificationPanel');
    if (panel.style.display === 'none' || panel.style.display === '') {
        panel.style.display = 'block';
    } else {
        panel.style.display = 'none';
    }
  });
  