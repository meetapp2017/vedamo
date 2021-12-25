$(function () {

	$('#db_message').hide();
	$('#db_ready').hide();
	$('#pagination').hide();

	//service.db_init();

	$('#cteate_db').click(function () {
		service.db_init();
	});

	$('#search_result').click(function () {
		var text_search = $('#text_search').val();

		var params = {
			text_search: text_search,
			country_code: "",
			start_from: 0,
			method: 'getReport'
		}

		localStorage.setItem('filter', JSON.stringify(params));
		service.getReportByParams(params);

	});

	$('#countries').on('change', function () {

		var params = {
			text_search: "",
			country_code: this.value,
			start_from: 0,
			method: 'getReport'
		}

		localStorage.setItem('filter', JSON.stringify(params));
		service.getReportByParams(params);

	})

	$('#next_page').click(function () {
		var params = JSON.parse(localStorage.getItem('filter'));
		params.start_from += 10;
		service.page++;
		service.getReportByParams(params);
		localStorage.setItem('filter', JSON.stringify(params));

	});

	$('#back_page').click(function () {
		var params = JSON.parse(localStorage.getItem('filter'));
		if (params.start_from <= 0) return;

		params.start_from -= 10;
		service.page--;
		service.getReportByParams(params);
		localStorage.setItem('filter', JSON.stringify(params));

	});

});

var service = {
	start_from: 0,
	total_records: 0,
	page: 1,

	request: function (url, params, callback) {
		$.ajax({
			url: url,
			type: 'POST',
			dataType: 'json',
			data: params,
			cashe: false,
			success: function (users) {
				callback(users);
			},
			error: function (err) {
				callback(err);
			}
		});
	},

	db_init: function () {

		var mainObject = this;
		$('#db_message').show();

		this.request("api/db.php", {}, function (state) {
			$('#db_message').hide();
			$('#prepare_db').hide();
			$('#db_ready').show();
			mainObject.init_countries();
		});

	},

	init_countries: function () {

		this.request("api/app.php", { method: 'getCountries' }, function (countries) {

			var drop_down_countries = $("#countries");

			for (var i in countries) {
				country = countries[i];
				drop_down_countries.append($("<option />").val(country.code).text(country.name));

			}
		})
	},

	getReportByParams: function (params) {

		var mainObject = this;

		var table = '<table table class="table table-dark" > ' +
			'<thead>' +
			'<tr>' +
			'<th scope="col"><a href="#">ID</a></th>' +
			'<th scope="col"><a href="#">Username</a></th>' +
			'<th scope="col"><a href="#">Last Name</a></th>' +
			'<th scope="col"><a href="#">Email</a></th>' +
			'<th scope="col"><a href="#">Country</a></th>' +
			'</tr>' +
			'</thead>' +
			'<tbody>' +

			this.request("api/app.php", params, function (users) {

				$('#main_table').html("");

				mainObject.total_records = users.length;

				for (var i in users) {
					var user = users[i];

					if (user.user_count > 0)
					mainObject.total_records = user.user_count;

					table +=
						'<tr>' +
						'<td>' + user.user_id + '</td>' +
						'<td>' + user.username + '</td>' +
						'<td>' + user.lastname + '</td>' +
						'<td>' + user.email + '</td>' +
						'<td>' + user.country_name + '</td>' +
						'</tr>';
				}


				$('#main_table').html(table + '</tbody></table>');
				$('#pagination').show();
				$('#total_records').html(mainObject.total_records + "/ page " + mainObject.page);

			});
	},
}
