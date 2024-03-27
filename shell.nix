let 
  pkgs = import <nixpkgs> {};
in pkgs.mkShell {
  nativeBuildInputs = with pkgs; [
    php
    phpPackages.composer
    git
    nodejs
  ];
}
