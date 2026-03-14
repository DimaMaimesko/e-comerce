terraform {
    backend "s3" {
        bucket         = "devops-directive-tf-state-dima2"
        key            = "03-basics/web-app/terraform.tfstate"
        region         = "us-east-2"
        dynamodb_table = "terraform-state-locking-2"
        encrypt        = true
    }

    required_providers {
        aws = {
            source  = "hashicorp/aws"
            version = "~> 3.0"
        }
    }
}
