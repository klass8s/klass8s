# Klass8s
Klass8s allows you to generate Kubernetes manifests from PHP classes.

## Installation
To generate manifests, Klass8s operates on Kubernetes API objects, 
which are defined in [tequila/k8schema](https://github.com/tequila/k8schema).
Classes in `tequila/k8schema` are generated from Kubernetes OpenAPI specifications
by the [tequila/k8schema-generator](https://github.com/tequila/k8schema-generator).
`tequila/k8schema` has a minor release for every supported Kubernetes API version
(usually, four latest minor releases). Check your Kubernetes version 
and install a corresponding version of `tequila/k8schema` - for example, 
if you're using Kubernetes v1.32, install `tequila/k8schema` as follows:

```bash
composer require tequila/k8schema:~1.32.0
```

> [!IMPORTANT]
> Always use the tilde (`~`) operator with all three version numbers (e.g., `~1.32.0`) 
> when installing `tequila/k8schema`. 
> Using the caret (`^`) operator will automatically update the library to the newest minor Kubernetes version, 
> which may break your manifests if you're not using the newest version of Kubernetes.
