package app.controllers;

import app.models.UserHelper;
import digirent.jersey.inject.Component;
import digirent.view.View;

import javax.ws.rs.*;
import javax.ws.rs.core.*;

import static digirent.util.ParameterUtils.params;

@Path("/")
@Produces(MediaType.TEXT_HTML)
public class PagesController {
    @GET
    public Response home(@Component UserHelper userHelper) {
        return index(userHelper);
    }

    @GET
    @Path("index.html")
    public Response index(@Component UserHelper userHelper) {
        return page(userHelper, "index");
    }

    @GET
    @Path("pages/{name}.html")
    public Response page(
            @Component UserHelper userHelper,
            @PathParam("name") String name) {
        View view = new View("pages/" + name, params(
                "account", userHelper.getAccount().orNull()));
        return Response.ok(view).build();
    }
}
