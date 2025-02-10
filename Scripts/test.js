document.addEventListener('DOMContentLoaded',()=>{
    document.getElementById("emailForm").addEventListener("submit", function(event) {
        event.preventDefault();
    
        // Get the value of the textarea
        var emails = document.getElementById("emails").value.trim();
    
        // Check if the input is a valid JSON
        try {
            var parsedEmails = JSON.parse(emails);
            if (!Array.isArray(parsedEmails)) {
                throw new Error("The input is not a valid array.");
            }
    
            // Send the POST request to the PHP API
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "/PHP/Validation/validate_emails.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            
            console.log("Loading...");
            

            // Send the emails as a JSON string
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Display the API response
                    document.getElementById("response").textContent = xhr.responseText;
                } else {
                    
                    document.getElementById("response").textContent = "Error: " + xhr.status;
                }
            };
    
            // Prepare data in URL-encoded format
            var data = "emails=" + encodeURIComponent(JSON.stringify(parsedEmails));
    
            // Send request
            xhr.send(data);
        } catch (e) {
            document.getElementById("response").textContent = "Invalid JSON format: " + e.message;
        }
    });
})