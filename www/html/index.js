$(document).ready(function () {
  // Load all tickets by default
  loadTickets('');

  // Add event listener to the status filter select element
  // $('#status').change(function () {
  //   var status = $(this).val();
  //   loadTickets(status);
  // });
});

function loadTickets(status) {
  // Send an AJAX request to get the tickets from the database
  $.ajax({
    url: 'get_tickets.php',
    type: 'POST',
    data: { status: status },
    success: function (response) {
      // Parse the JSON response and generate the ticket rows
      var tickets = JSON.parse(response);
      var rows = '';
      for (var i = 0; i < tickets.length; i++) {
        var ticket = tickets[i];
        rows += '<tr>';
        rows += '<td>' + ticket.id + '</td>';
        rows += '<td><a href="ticket_details.php?id=' + ticket.id + '">' + ticket.subject + '</a></td>';
        rows += '<td>' + ticket.status + '</td>';                rows += '</tr>';
      }
      // Replace the existing ticket rows with the new ones
      $('#ticket-table-body').html(rows);
    }
  });
}


const filtering = (e) => {
  let status = this.event.target.value
  $.ajax({
    url: 'get_tickets.php',
    type: 'POST',
    data: { status: status },
    success: (response) => {
      // Parse the JSON response and generate the ticket rows
      var tickets = JSON.parse(response);
      var rows = '';
      for (var i = 0; i < tickets.length; i++) {
        var ticket = tickets[i];
        if (String(ticket?.status)?.toLowerCase() !== String(status)?.toLowerCase()) {
          continue;
        }
        rows += '<tr>';
        rows += '<td>' + ticket.id + '</td>';
        rows += '<td><a href="ticket_details.php?id=' + ticket.id + '">' + ticket.subject + '</a></td>';
        rows += '<td>' + ticket.status + '</td>';
        
        rows += '</tr>';
      }
      // Replace the existing ticket rows with the new ones
      $('#ticket-table-body').html(rows);
    }
  });
}

