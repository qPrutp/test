		</div>
		<hr/>
	</div>
	
<footer class="footer">
	<h3>
		Vasyl Savitskyy &copy; 2018
	</h3>
</footer>

<script src="https://code.jquery.com/jquery-3.0.0.min.js"></script>
<script>
	$("#url").change(function(){
	  if($("#url").val()=="")
	    $("#button").prop('disabled', true)
	  else
	    $("#button").prop('disabled', false)
	});
</script>
</body>
</html>