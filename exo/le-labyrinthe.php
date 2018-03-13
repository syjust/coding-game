<?php
/**
 * Auto-generated code below aims at helping you parse
 * the standard input according to the problem statement.
 *
 * La structure se présente comme un labyrinthe rectangulaire composé de cellules. Une fois dans le labyrinthe,
 * Kirk peut aller dans les directions suivantes: haut, bas, gauche et droite (UP, DOWN, LEFT, RIGHT).
 *
 *
 * Format du labyrinthe
 * Le labyrinthe est fourni en entrée sous forme ASCII. Le caractère # représente un mur, le caractère.
 * représente un espace vide, la lettre T représente votre position de départ,
 * la lettre C représente la salle de commande
 * et le caractère ? représente une cellule que vous n'avez pas encore scannée.
 *
 **/

fscanf(STDIN, "%d %d %d",
    $R, // number of rows.
    $C, // number of columns.
    $A // number of rounds between the time the alarm countdown is activated and the time the alarm goes off.
);

// game loop
while (TRUE)
{
    fscanf(STDIN, "%d %d",
        $KR, // row where Kirk is located.
        $KC // column where Kirk is located.
    );
    for ($i = 0; $i < $R; $i++)
    {
        fscanf(STDIN, "%s",
            $ROW // C of the characters in '#.TC?' (i.e. one line of the ASCII maze).
        );
        error_log($ROW);
    }

    // Write an action using echo(). DON'T FORGET THE TRAILING \n
    // To debug (equivalent to var_dump): error_log(var_export($var, true));

    echo("RIGHT\n"); // Kirk's next move (UP DOWN LEFT or RIGHT).
}
?>
