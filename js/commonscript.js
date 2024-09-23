function showNotification(message, type = 'danger') {
    const notificationElement = document.getElementById('notificationSuccess');
    notificationElement.textContent = message;
    notificationElement.className = `alert alert-${type}`;
    notificationElement.classList.remove('d-none');
    
    // Hide the notification after 5 seconds
    setTimeout(() => {
      notificationElement.classList.add('d-none');
    }, 3000);
  }