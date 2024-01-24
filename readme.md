# Project Guide
 *  This project is created so you can use and test three API's without the need of any aditional tools to do so. All that is needed for installment will be listed down bellow.
 * The testing of the API's is possible through a swagger interface configured around the framework's used.

# Installlation Requirements

* To get he project up and running you only need a Linux VM running on your OS, such as WSL or virtual box.
* Use git to clone the project.
* Make sure the docker engine is installed and run docker **compose up -d**, and that's all that is needed to make it work.
* After the first main step, please run **make migrate** in the project root, if you get a connection refused just wait a few seconds until it works.
* If something goes wrong with the database fixing within the container might be required, but you can always take the container down and repeat the commands.
* After having the docker containers running successfully, just visit localhost:8080/api so you can test the project.

<br>

## Project considerations
* Authentication was left out due to the already extensive configuration and its needs not being too relevant in my view, as well as protecting the endpoints.
* The project was configured by leveraging the [Symfony Framework](https://symfony.com/doc/current/index.html) and the [API Platform](https://api-platform.com/docs/distribution/).
* Filtering relationships were left out of the capabilities configured in the project, but it is possible to filter and order a resource by any field available to that resource.
* Simply run php bin/console list you will find all the commands there


<br>


## Project structure
### This project takes concepts from the following architectural patterns:
<ul>
  <li>Clean Architecture</li>
  <li>Action-Domain Responder</li>
  <li>Domain-Driven Design</li>
  <li>CQRS</li>
</ul>


### Implementation specification
* The entrypoint to the App is in the Infrastructure layer, API Resource folder. Refer to the API Platform and Symfony framework docs and API for more details.

