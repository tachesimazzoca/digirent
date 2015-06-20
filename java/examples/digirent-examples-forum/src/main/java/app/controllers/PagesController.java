package app.controllers;

import app.models.UserHelper;
import digirent.jersey.inject.UserContext;
import digirent.view.View;

import javax.ws.rs.GET;
import javax.ws.rs.Path;
import javax.ws.rs.PathParam;
import javax.ws.rs.Produces;
import javax.ws.rs.core.MediaType;
import javax.ws.rs.core.Response;

import static digirent.util.ParameterUtils.params;

@Path("/")
@Produces(MediaType.TEXT_HTML)
public class PagesController {
    @GET
    public Response home(@UserContext UserHelper userHelper) {
        return index(userHelper);
    }

    @GET
    @Path("index.html")
    public Response index(@UserContext UserHelper userHelper) {
        return page(userHelper, "index");
    }

    @GET
    @Path("pages/{name}.html")
    public Response page(
            @UserContext UserHelper userHelper,
            @PathParam("name") String name) {
        View view = new View("pages/" + name, params(
                "account", userHelper.getAccount().orNull()));
        return Response.ok(view).build();
    }
}
