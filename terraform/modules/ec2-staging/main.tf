resource "aws_key_pair" "this" {
  key_name   = "${var.project_name}-${var.environment}-key"
  public_key = file(var.public_key_path)

  tags = {
    Name        = "${var.project_name}-${var.environment}-key"
    Project     = var.project_name
    Environment = var.environment
  }
}

resource "aws_security_group" "this" {
  name        = "${var.project_name}-${var.environment}-sg"
  description = "Security group for ${var.project_name} ${var.environment}"
  vpc_id      = var.vpc_id

  ingress {
    description = "SSH from trusted IP"
    from_port   = 22
    to_port     = 22
    protocol    = "tcp"
    cidr_blocks = [var.ssh_allowed_cidr]
  }

  ingress {
    description = "HTTP"
    from_port   = 80
    to_port     = 80
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }

  ingress {
    description = "HTTPS"
    from_port   = 443
    to_port     = 443
    protocol    = "tcp"
    cidr_blocks = ["0.0.0.0/0"]
  }
    ingress {
        description = "k3s API (kubectl)"
        from_port   = 6443
        to_port     = 6443
        protocol    = "tcp"
        cidr_blocks = [var.ssh_allowed_cidr]
    }

    ingress {
        description = "ArgoCD UI"
        from_port   = 30443
        to_port     = 30443
        protocol    = "tcp"
        cidr_blocks = [var.ssh_allowed_cidr]
    }

  egress {
    description = "All outbound"
    from_port   = 0
    to_port     = 0
    protocol    = "-1"
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = {
    Name        = "${var.project_name}-${var.environment}-sg"
    Project     = var.project_name
    Environment = var.environment
  }
}

resource "aws_instance" "this" {
  ami                    = var.ami_id
  instance_type          = var.instance_type
  subnet_id              = var.subnet_id
  vpc_security_group_ids = [aws_security_group.this.id]
  key_name               = aws_key_pair.this.key_name

  associate_public_ip_address = true

  root_block_device {
    volume_size = var.root_volume_size_gb
    volume_type = "gp3"
  }

  tags = {
    Name        = "${var.project_name}-${var.environment}"
    Project     = var.project_name
    Environment = var.environment
  }
}

resource "aws_eip" "this" {
  # domain   = "vpc"
  instance = aws_instance.this.id

  tags = {
    Name        = "${var.project_name}-${var.environment}-eip"
    Project     = var.project_name
    Environment = var.environment
  }
}
