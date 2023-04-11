const wcElement = document.getElementsByTagName("descope-wc")[0];

const onSuccess = (e) => {
  // console.log("userdetails", e);
  const sessionToken = e.detail.sessionJwt;
  const refreshToken = e.detail.refreshJwt;
  // console.log("sessionToken", sessionToken);
  // console.log("refreshToken", refreshToken);

  createToken(
    e?.detail?.user,
    sessionToken,
    refreshToken,
    e.target.id,
    e.target.getAttribute("redirect_url"),
    e.target.getAttribute("project-id")
  );
};

const onError = (err) => console.log(err);

wcElement.addEventListener("success", onSuccess);
wcElement.addEventListener("error", onError);

function createToken(
  userDetails,
  sessionToken,
  refreshToken,
  id,
  redirectURL,
  projectId
) {
  var formData = new FormData();
  formData.append("userId", userDetails.userId);
  formData.append("userName", userDetails.name);
  formData.append("sessionToken", sessionToken);
  formData.append("refreshToken", refreshToken);
  formData.append("idDescope", id);
  formData.append("projectId", projectId);

  var xmlHttp = new XMLHttpRequest();
  let getUrl = window.location;
  let baseUrl =
    getUrl.protocol + "//" + getUrl.host + "/" + getUrl.pathname.split("/")[1];

  xmlHttp.onreadystatechange = function () {
    if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
      console.log("Response", xmlHttp.responseText);
      window.location = `${baseUrl}/${redirectURL}`;
    }
  };
  xmlHttp.open(
    "post",
    `${baseUrl}/wp-content/plugins/descope-wp/src/descope-token.php`
  );
  xmlHttp.send(formData);
}
