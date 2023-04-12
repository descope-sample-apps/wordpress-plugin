function createToken(
  userDetails,
  sessionToken,
  refreshToken,
  redirectURL,
  projectId
) {
  var formData = new FormData();
  formData.append("userId", userDetails.userId);
  formData.append("userName", userDetails.name);
  formData.append("sessionToken", sessionToken);
  formData.append("refreshToken", refreshToken);
  // formData.append("idDescope", id);
  formData.append("projectId", projectId);

  var xmlHttp = new XMLHttpRequest();
  let getUrl = window.location;
  let baseUrl = getUrl.protocol + "//" + getUrl.host;

  xmlHttp.onreadystatechange = function () {
    if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
      // console.log("Response", xmlHttp.responseText);
      window.location = `${baseUrl}/${redirectURL}`;
    }
  };
  xmlHttp.open(
    "post",
    `${baseUrl}/wp-content/plugins/wordpress-plugin/src/descope-token.php`
  );
  xmlHttp.send(formData);
}

const onSuccess = (e) => {
  const sessionToken = e.detail.sessionJwt;
  const refreshToken = e.detail.refreshJwt;
  // sdk.refresh();
  createToken(
    e?.detail?.user,
    sessionToken,
    refreshToken,
    e.target.getAttribute("redirect_url"),
    e.target.getAttribute("project-id")
  );
};

const onError = (err) => console.log(err);



async function inject_flow(projectId, flowId, redirectUrl) {
  const sdk = Descope({ projectId:projectId, persistTokens: true, autoRefresh: true });
  const sessionToken = sdk.getSessionToken();
  const refreshToken = sdk.getRefreshToken();
  const notValidToken = sessionToken && sdk.isJwtExpired(sessionToken);
  if (sessionToken && !notValidToken) {
    const user = await sdk.me();
    createToken(user.data, sessionToken, refreshToken, redirectUrl, projectId);
  }
  else {
    const e = document.getElementById("descope_flow_div");
    e.innerHTML = `<descope-wc project-id=${projectId} flow-id=${flowId} redirect_url=${redirectUrl}></descope-wc>`;
    const wcElement = document.getElementsByTagName("descope-wc")[0];
    if (wcElement) {
      wcElement.addEventListener("success", onSuccess);
      wcElement.addEventListener("error", onError);
    }
  }
}



