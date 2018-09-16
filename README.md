# Moritz Media Git Deployment

## Main Code Directories
The website files are in
```
    ~/moritzMediaProject/mypie/ // Website files
    ~/moritzMediaStage/mypie/   // Git bare repo
```

## GitHub Project
All source code is on https://github.com/peareye/mypie
Feel free to contact us through GitHub if you are working on this website!

## To Contribute or Work on the Website
All of the source code is in a bare Git repo, separate from the web application files, in `~/moritzMediaStage/mypie/`.

To work on this website, clone `~/moritzMediaStage/mypie/` to your local machine. Or, feel free to ask to join the GitHub source on https://github.com/peareye/mypie.

## Code Deployment
Do **NOT** directly change files in the project. All changes can be committed to your local Git and pushed to the bare repo for mypie.london. If you change files locally, those changes _will_ be overwritten if we push code.

To deploy changes, simply push to the bare Git repo in `~/moritzMediaStage/mypie/`. That's it.

In the bare repo is a post-receive hook, that runs a shell script to then in turn check out hard the new files in
`~/moritzMediaProject/mypie/` and clear the Twig file cache.

Moritz Media
