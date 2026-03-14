provider "aws" {
  region = var.aws_region
}

data "aws_vpc" "default" {
  default = true
}

data "aws_subnets" "default" {
  filter {
    name   = "vpc-id"
    values = [data.aws_vpc.default.id]
  }
}

module "staging_server" {
  source = "../../modules/ec2-staging"

  project_name         = var.project_name
  environment          = var.environment
  aws_region           = var.aws_region
  vpc_id               = data.aws_vpc.default.id
  subnet_id            = data.aws_subnets.default.ids[0]
  instance_type        = var.instance_type
  ami_id               = var.ami_id
  root_volume_size_gb  = var.root_volume_size_gb
  ssh_allowed_cidr     = var.ssh_allowed_cidr
  public_key_path      = var.public_key_path
}
