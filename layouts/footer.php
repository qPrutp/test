		</div>
		<hr/>
	</div>
	
<footer class="footer">
	<h3>
		Vasyl Savitskyy &copy; 2018
	</h3>
</footer>

<script src="https://code.jquery.com/jquery-3.0.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsgrid/1.4.1/jsgrid.min.js"></script>
<script>
    $(document).on('submit', '#parser-form', function () {
        $.ajax({
            url: $('#parser-form').attr('action'),
            data: {site: $('#parser-form #site').val()},
            type: 'post',
            success: function (data) {
                $(".table").jsGrid({
                    width: "100%",
                    height: "600px",
                    data: JSON.parse(data),
                    fields: [
                        {name: "id", type: "text", title: "№"},
                        {name: "name", type: "text", title: "Название проверки"},
                        {name: "text.status", type: "text", title: "Результат"},
                        {name: "status", type: "text", title: "Статус"},
                        {name: "text.comment", type: "text", title: "Комментарий"}
                    ]
                });
            }
        });
        return false;
    });
</script>

</body>
</html>