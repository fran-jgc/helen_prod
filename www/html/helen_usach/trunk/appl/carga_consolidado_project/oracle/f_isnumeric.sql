CREATE OR REPLACE FUNCTION f_isnumeric (p_string_value IN VARCHAR2)
RETURN VARCHAR2
    AS

    test_value NUMERIC;
    BEGIN
        BEGIN
            test_value := TO_NUMBER (p_string_value);
            RETURN 'S';

            EXCEPTION
            WHEN OTHERS THEN
            RETURN 'N';
        END;
    END f_isnumeric;

