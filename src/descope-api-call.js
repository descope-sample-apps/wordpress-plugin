const wcElement = document.getElementsByTagName("descope-wc")[0];

const onSuccess = (e) => {
  // console.log("userdetails", e);
  const sessionToken = e.detail.sessionJwt;
  const refreshToken = e.detail.refreshJwt;
  // console.log("sessionToken", sessionToken);
  // console.log("refreshToken", refreshToken);
  sdk.refresh();

  createToken(
    e?.detail?.user,
    sessionToken,
    refreshToken,
    e.target.id,
    e.target.getAttribute("redirect_url")
  );
};

const onError = (err) => console.log(err);

wcElement.addEventListener("success", onSuccess);
wcElement.addEventListener("error", onError);

function createToken(userDetails, sessionToken, refreshToken, id, redirectURL) {
  var formData = new FormData();
  formData.append("userId", userDetails.userId);
  formData.append("userName", userDetails.name);
  formData.append("sessionToken", sessionToken);
  formData.append("idDescope", id);

  var xmlHttp = new XMLHttpRequest();
  let getUrl = window.location;
  let baseUrl =
    getUrl.protocol + "//" + getUrl.host + "/" + getUrl.pathname.split("/")[1];

  xmlHttp.onreadystatechange = function () {
    if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
      console.log("Response", xmlHttp.responseText);
      console.log(`${baseUrl}/${redirectURL}/`);
      window.location = `${baseUrl}/${redirectURL}`;
    }
  };
  xmlHttp.open(
    "post",
    `${baseUrl}/wp-content/plugins/descope-wp/descope-token.php`
  );
  xmlHttp.send(formData);
}
