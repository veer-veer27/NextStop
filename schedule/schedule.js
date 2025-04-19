document.querySelectorAll('.book-btn').forEach(button => {
    button.addEventListener('click', function() {
        const busDetails = this.dataset;
        openPopup(busDetails);
    });
});

function openPopup(details) {
    document.getElementById("popupBusName").innerText = details.busName;
    document.getElementById("popupDeparture").innerText = details.departure;
    document.getElementById("popupDestination").innerText = details.destination;
    document.getElementById("popupDepartureTime").innerText = details.departureTime;
    document.getElementById("popupArrivalTime").innerText = details.arrivalTime;
    document.getElementById("popupPrice").innerText = details.price;
    
    updateTotalPrice();
    document.getElementById("bookingPopup").style.display = "block";
}

function closePopup() {
    document.getElementById("bookingPopup").style.display = "none";
}

function updateTotalPrice() {
    const price = parseFloat(document.getElementById("popupPrice").innerText);
    const passengers = document.getElementById("passengerCount").value;
    const total = (price * passengers).toFixed(2);
    document.getElementById("totalPrice").innerText = total;
}

document.getElementById("passengerCount").addEventListener('input', updateTotalPrice);

function confirmBooking() {
    const busName = document.getElementById("popupBusName").innerText;
    const departure = document.getElementById("popupDeparture").innerText;
    const destination = document.getElementById("popupDestination").innerText;
    const departureTime = document.getElementById("popupDepartureTime").innerText;
    const arrivalTime = document.getElementById("popupArrivalTime").innerText;
    const price = document.getElementById("popupPrice").innerText;
    const passengers = document.getElementById("passengerCount").value;
    const totalPrice = document.getElementById("totalPrice").innerText;

    // Redirect to payment page
    const params = new URLSearchParams({
        bus_name: busName,
        departure: departure,
        destination: destination,
        departure_time: departureTime,
        arrival_time: arrivalTime,
        price: price,
        passengers: passengers,
        total_price: totalPrice
    });

    window.location.href = "payment.php?" + params.toString();
}
z