# GNUstep Developer Site
The purpose of this site is to contain the developer centric information.

## Building
### Installation of dependencies

First, please have Conda installed on your computer. If it's not installed, please install [Miniforge3](https://conda-forge.org/miniforge/), which includes Conda and a conda-forge based Python environment. You can install Miniforge3 using the following command:

```bash
wget "https://github.com/conda-forge/miniforge/releases/latest/download/Miniforge3-$(uname)-$(uname -m).sh"
bash Miniforge3-$(uname)-$(uname -m).sh
rm Miniforge3-$(uname)-$(uname -m).sh
```

Close and reopen your shell, and run:

```bash
# Prevent Conda from polluting your environment when you're not working on Conda-managed projects.
conda config --set auto_activate_base false
```

Now, you can use Conda to install the dependencies.

```bash
mamba env create -f environment.yml
mamba activate NewDocumentation
```

If you modify `environment.yml`, please run

```bash
mamba env update -f environment.yml
```

Occassionally, you should update all the packages to the latest versions:

```bash
mamba activate NewDocumentation
mamba update --all
```

### Building

To make the docs in HTML format, run:

```bash
make html
```

Then you can read it in your browser:

```bash
firefox build/html/index.html
```

There are other output formats, but they might not work.
