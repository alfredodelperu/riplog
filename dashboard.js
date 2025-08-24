fetch("api.php")
  .then(r => r.json())
  .then(data => {
    console.log(data);
  });
