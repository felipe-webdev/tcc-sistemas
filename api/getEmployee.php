<?php
  header("Access-Control-Allow-Origin: *");
  header("Content-Type: application/json; charset=UTF-8");
  header("Access-Control-Allow-Methods: POST");
  header("Access-Control-Max-Age: 3600");
  header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
  header("Access-Control-Allow-Credentials: true");

  $data        = json_decode(file_get_contents("php://input"));
  $id_employee = $data->id_employee;

  require_once "dbconnect.php";


  // FUNCIONARIO
  $sql_employee = "SELECT
    p.id_entidade    AS id_entity,
    p.id             AS id_person,
    f.id             AS id_employee,
    f.id_cargo       AS id_job_type,
    c.nome           AS job_type,
    a.id             AS id_job_depart,
    a.nome           AS job_depart,
    c.salario        AS salary,
    p.nome           AS `name`,
    p.sobrenome      AS surname,
    EXTRACT(DAY      FROM p.nascimento) AS birth_d,
    EXTRACT(MONTH    FROM p.nascimento) AS birth_m,
    EXTRACT(YEAR     FROM p.nascimento) AS birth_y,
    IFNULL(p.cpf, '')AS cpf,
    f.ativo          AS active
  FROM funcionarios  AS f
  INNER JOIN pessoas AS p
    ON f.id_pessoa = p.id
  INNER JOIN cargos  AS c
    ON f.id_cargo = c.id
  INNER JOIN areas   AS a
    ON c.id_area = a.id
  WHERE f.id = ?;";

  try {
    $statement = $PDO->prepare($sql_employee);
    $statement->execute([$id_employee]);
  } catch ( PDOException $e ){
    echo(
      json_encode([
        'success' => false,
        'error'   => 'sql',
        'msg'     => $e->getMessage()
      ])
    );
    exit;
  }
  $employee  = $statement->fetch();
  $id_person = $employee['id_person'];


  // DEPENDENTES
  $sql_family = "SELECT
    d.id                       AS id_family,
    d.id_tipo                  AS id_family_type,
    dt.descricao               AS family_type,
    d.id_pessoa                AS id_person,
    p.nome                     AS `name`,
    p.sobrenome                AS surname,
    EXTRACT(DAY                FROM p.nascimento) AS birth_d,
    EXTRACT(MONTH              FROM p.nascimento) AS birth_m,
    EXTRACT(YEAR               FROM p.nascimento) AS birth_y,
    IFNULL(p.cpf, '')          AS cpf
  FROM dependentes             AS d
  INNER JOIN dependentes_tipos AS dt
    ON d.id_tipo = dt.id
  INNER JOIN pessoas           AS p
    ON d.id_pessoa = p.id
  WHERE d.id_funcionario = ?;";

  try {
    $statement = $PDO->prepare($sql_family);
    $statement->execute([$id_employee]);
  } catch ( PDOException $e ){
    echo(
      json_encode([
        'success' => false,
        'error'   => 'sql',
        'msg'     => $e->getMessage()
      ])
    );
    exit;
  }
  $family = $statement->fetchAll();


  // USUARIO
  $sql_user = "SELECT
    null                     AS `create`,
    u.id_grupo               AS id_user_group,
    g.nome                   AS user_group,
    u.id                     AS id_user,
    u.usuario                AS user,
    null                     AS postfix,
    null                     AS pass
  FROM usuarios              AS u
  INNER JOIN usuarios_grupos AS g
    ON u.id_grupo = g.id
  WHERE u.id_funcionario = ?;";

  try {
    $statement = $PDO->prepare($sql_user);
    $statement->execute([$id_employee]);
  } catch ( PDOException $e ){
    echo(
      json_encode([
        'success' => false,
        'error'   => 'sql',
        'msg'     => $e->getMessage()
      ])
    );
    exit;
  }
  $user = $statement->fetch();


  // ENDERECO
  $sql_address = "SELECT
    e.id                     AS id_address,
    e.id_tipo                AS id_address_type,
    et.descricao             AS address_type,
    e.logradouro             AS `line`,
    e.numero                 AS `number`,
    IFNULL(e.complemento_numero, '')     AS number_info,
    e.bairro                 AS district,
    e.cidade                 AS city,
    IFNULL(e.estado, '')                 AS `state`,
    e.uf,
    IFNULL(e.cep, '')                    AS cep,
    IFNULL(e.observacao, '')             AS obs
  FROM enderecos             AS e
  INNER JOIN enderecos_tipos AS et
    ON e.id_tipo = et.id
  WHERE e.id_pessoa = ?;";

  try {
    $statement = $PDO->prepare($sql_address);
    $statement->execute([$id_person]);
  } catch ( PDOException $e ){
    echo(
      json_encode([
        'success' => false,
        'error'   => 'sql',
        'msg'     => $e->getMessage()
      ])
    );
    exit;
  }
  // $address = $statement->fetchAll();
  $address = $statement->fetch();
  if(!$address){ 
    $address = [
      'id_address'      => '',
      'id_address_type' => '',
      'address_type'    => '',
      'line'            => '',
      'number'          => '',
      'number_info'     => '',
      'district'        => '',
      'city'            => '',
      'state'           => '',
      'cep'             => '',
      'obs'             => ''
    ];
  }

  // TELEFONES
  $sql_phone = "SELECT
    id          AS id_phone,
    contato     AS `phone`,
    IFNULL(observacao, '')  AS obs
  FROM contatos
  WHERE id_tipo = 1 AND id_pessoa = ?;";

  try {
    $statement = $PDO->prepare($sql_phone);
    $statement->execute([$id_person]);
  } catch ( PDOException $e ){
    echo(
      json_encode([
        'success' => false,
        'error'   => 'sql',
        'msg'     => $e->getMessage()
      ])
    );
    exit;
  }
  $phones = $statement->fetchAll();


  // EMAILS
  $sql_email = "SELECT
    id          AS id_email,
    contato     AS email,
    IFNULL(observacao, '')  AS obs
  FROM contatos
  WHERE id_tipo = 2 AND id_pessoa = ?;";

  try {
    $statement = $PDO->prepare($sql_email);
    $statement->execute([$id_person]);
  } catch ( PDOException $e ){
    echo(
      json_encode([
        'success' => false,
        'error'   => 'sql',
        'msg'     => $e->getMessage()
      ])
    );
    exit;
  }
  $emails = $statement->fetchAll();


  echo(
    json_encode([
      'success'  => true,
      'employee' => $employee,
      'user'     => $user,
      'family'   => $family,
      'address'  => $address,
      'phones'   => $phones,
      'emails'   => $emails
    ])
  );
  exit;
?>